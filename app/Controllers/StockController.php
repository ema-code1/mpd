<?php namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class StockController extends Controller
{
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper('url');
        helper('form');
    }

    public function index()
    {
        // Traer TODOS los libros con stock calculado dinámicamente
        $sql = "SELECT 
                    l.id,
                    l.titulo,
                    GREATEST(COALESCE(SUM(
                        CASE 
                            WHEN sc.tipo = 'ingreso' THEN sv.cantidad 
                            WHEN sc.tipo = 'egreso' THEN -sv.cantidad 
                            ELSE 0 
                        END
                    ), 0), 0) as stock
                FROM libros l
                LEFT JOIN stock_values sv ON l.id = sv.libro_id
                LEFT JOIN stock_columns sc ON sv.column_id = sc.id
                GROUP BY l.id, l.titulo
                ORDER BY l.titulo";

        $libros = $this->db->query($sql)->getResultArray();

        // Traer columnas de ingresos/egresos
        $cols = $this->db->table('stock_columns')->orderBy('created_at','ASC')->get()->getResultArray();

        // Mapear valores por column_id -> libro_id
        $values = [];
        if (!empty($cols)) {
            $colIds = array_column($cols, 'id');
            $rows = $this->db->table('stock_values')->whereIn('column_id', $colIds)->get()->getResultArray();
            foreach ($rows as $r) {
                $values[$r['column_id']][$r['libro_id']] = (int)$r['cantidad'];
            }
        }

        echo view('templates/header');
        echo view('dashboards/stock', [
            'libros' => $libros,
            'cols' => $cols,
            'values' => $values
        ]);
    }

    public function createColumn()
    {
        $request = Services::request();
        $name = $request->getPost('name') ?? 'Ingreso ' . date('Y-m-d H:i:s');
        $tipo = $request->getPost('tipo') ?? 'ingreso';

        $this->db->transStart();
        
        $this->db->table('stock_columns')->insert([
            'name' => $name,
            'tipo' => $tipo,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $colId = $this->db->insertID();

        // Insertar filas con valor 0 para TODOS los libros
        $libros = $this->db->table('libros')->select('id')->get()->getResultArray();
        
        $insert = [];
        foreach ($libros as $l) {
            $insert[] = [
                'column_id' => $colId,
                'libro_id' => $l['id'],
                'cantidad' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($insert)) {
            $this->db->table('stock_values')->insertBatch($insert);
        }
        
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            return $this->response->setJSON([
                'status' => 'ok', 
                'column_id' => $colId, 
                'name' => $name, 
                'tipo' => $tipo
            ]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo crear la columna']);
        }
    }

    public function updateCell()
    {
        $request = Services::request();
        $colId = (int)$request->getPost('column_id');
        $libroId = (int)$request->getPost('libro_id');
        $delta = (int)$request->getPost('delta');

        // Leer fila actual
        $svTable = $this->db->table('stock_values');
        $row = $svTable->where(['column_id' => $colId, 'libro_id' => $libroId])->get()->getRowArray();

        if (!$row) {
            // Si no existe, crearla
            $newVal = max(0, $delta);
            $svTable->insert([
                'column_id' => $colId,
                'libro_id' => $libroId,
                'cantidad' => $newVal,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $newVal = max(0, $row['cantidad'] + $delta);
            $svTable->where('id', $row['id'])->update([
                'cantidad' => $newVal,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'new_value' => $newVal
        ]);
    }

    public function getStock()
    {
        $libroId = (int)$this->request->getPost('libro_id');
        
        $sql = "SELECT GREATEST(COALESCE(SUM(
                    CASE 
                        WHEN sc.tipo = 'ingreso' THEN sv.cantidad 
                        WHEN sc.tipo = 'egreso' THEN -sv.cantidad 
                        ELSE 0 
                    END
                ), 0), 0) as stock
                FROM stock_values sv
                JOIN stock_columns sc ON sv.column_id = sc.id
                WHERE sv.libro_id = ?";
        
        $result = $this->db->query($sql, [$libroId])->getRow();
        
        return $this->response->setJSON([
            'status' => 'ok',
            'stock' => $result ? $result->stock : 0
        ]);
    }

    public function deleteColumn()
    {
        $id = (int)$this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'No id provided']);
        }
        
        $this->db->table('stock_columns')->where('id', $id)->delete();
        
        return $this->response->setJSON(['status' => 'ok']);
    }
}