<?php

namespace App\Models;

use CodeIgniter\Model;

class ResenasModel extends Model
{
    protected $table = 'resenas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['libro_id', 'user_id', 'rating', 'descripcion', 'created_at'];
    protected $useTimestamps = false;

    public function getResenasByLibro($libro_id)
{
    return $this->select('resenas.*, users.name as user_name, users.foto_perfil as user_foto')
                ->join('users', 'users.id = resenas.user_id')
                ->where('libro_id', $libro_id)
                ->orderBy('created_at', 'DESC')
                ->findAll();
}

// Agregar estos métodos nuevos
public function getResenaById($resena_id)
{
    return $this->find($resena_id);
}

public function actualizarResena($resena_id, $data)
{
    $data['updated_at'] = date('Y-m-d H:i:s');
    return $this->update($resena_id, $data);
}

    public function getPromedioRating($libro_id)
    {
        $result = $this->selectAvg('rating', 'promedio')
                      ->where('libro_id', $libro_id)
                      ->first();
        
        return $result ? round($result['promedio'], 1) : 0;
    }

    public function getTotalResenas($libro_id)
    {
        return $this->where('libro_id', $libro_id)->countAllResults();
    }

    public function userYaReseno($user_id, $libro_id)
    {
        return $this->where('user_id', $user_id)
                   ->where('libro_id', $libro_id)
                   ->countAllResults() > 0;
    }

    public function getStatsResenas($libro_id)
    {
        return [
            'promedio' => $this->getPromedioRating($libro_id),
            'total' => $this->getTotalResenas($libro_id),
            'distribucion' => $this->getDistribucionRatings($libro_id)
        ];
    }

    private function getDistribucionRatings($libro_id)
    {
        $distribucion = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribucion[$i] = $this->where('libro_id', $libro_id)
                                   ->where('rating', $i)
                                   ->countAllResults();
        }
        return $distribucion;
    }
}