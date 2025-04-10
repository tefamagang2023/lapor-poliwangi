<?php

namespace Modules\Pelapor\Entities;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';
    protected $fillable = [ 'id', 'user_id', 'nama_pelapor', 
                            'nomor_pelapor', 'unit_id', 
                            'complaint_text', 'reply_text', 
                            'replied_by', 'reply_pelapor', 
                            'date_replied_by', 'date_reply_pelapor', 
                            'status', 'pending', 'forwarded_at', 'processed_at', 'completed_at', 
                            'created_at', 'updated_at'];

    public static function tambahLaporan($request) {
        $validator = Validator::make($request->all(), 
            ['complaint_text' => 'required', 'nomor_pelapor' => 'required',], 
            ['complaint_text.required' => 'Permasalahan wajib diisi', 'nomor_pelapor.required' => 'Nomor Hp wajib diisi']);
                if ($validator->fails()) {
                    return [
                        'status' => 'error',
                        'errors' => $validator->errors(),
                    ];
                }
                        
            // Simpan data pelapor
                Complaint::create([
                    'nama_pelapor' => Auth::user()->name,
                    'unit_id' => $request->unit_id,
                    'user_id' => 1,
                    'nomor_pelapor' => $request->nomor_pelapor,
                    'complaint_text' => $request->complaint_text,
                    'pending' => now(),
                    'status' => 'pending',
                ]);
                            
                return ['status' => 'success'];
    }
    
    protected static function newFactory()
    {
        return \Modules\Pelapor\Database\factories\ComplaintFactory::new();
    }
}
