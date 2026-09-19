@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h1 class="text-2xl font-bold text-gray-800">Impor RPP</h1>
            <p class="text-gray-600 mt-1">Impor RPP dalam format JSON untuk mengintegrasikan ke LMS dan CBT</p>
        </div>

        <form id="rppImportForm" class="px-6 py-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Data RPP (JSON)</label>
                <textarea id="rppJson" name="rpp_data" rows="15" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none font-mono text-sm" placeholder='{
  "rpp_metadata": {
    "mata_pelajaran": "Bahasa Indonesia",
    "kelas": "10",
    "topik_utama": "Teks Eksplanasi",
    "alokasi_waktu": "4 x 45 Menit"
  },
  "lms_integration": {
    "pertemuan_list": [
      {
        "pertemuan_ke": 1,
        "judul_topik": "Mengenal Struktur dan Ciri Kebahasaan Teks Eksplanasi",
        "deskripsi_aktivitas": "1. Baca teks eksplanasi...",
        "rekomendasi_bahan_ajar": ["E-Modul Aljabar Dasar", "Video Interaktif Variabel & Konstanta"]
      }
    ]
  },
  "cbt_integration": {
    "assessment_type": "Sumatif",
    "tujuan_pembelajaran_mapped": [
      {
        "tp_id": "TP_01",
        "deskripsi_tp": "Peserta didik mampu mengidentifikasi struktur organisasi teks eksplanasi...",
        "cbt_setup": {
          "jumlah_soal_direkomendasikan": 5,
          "tipe_soal": "Pilihan Ganda",
          "tingkat_kesulitan": "Mudah",
          "kata_kunci_indokator_soal": "Menandai paragraf yang merupakan &#039;deretan penjelas&#039;"
        }
      }
    ]
  }
}'></textarea>
                <p class="mt-2 text-xs text-gray-500">Format JSON sesuai dengan struktur yang ditentukan oleh sistem</p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="window.history.back()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Impor RPP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('rppImportForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const jsonText = document.getElementById('rppJson').value;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Mengimpor...</span>';
    
    try {
        const response = await fetch('{{ url("rpps") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ rpp_data: JSON.parse(jsonText) })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('RPP berhasil diimpor!\\n\\nID: ' + result.data.rpp_id + '\\nMata Pelajaran: ' + result.data.subject + '\\nTopik: ' + result.data.topic);
            window.location.href = '{{ url("rpps") }}';
        } else {
            alert('Gagal mengimpor RPP:\\n' + (result.message || 'Error tidak diketahui'));
            console.error(result.errors || result);
        }
    } catch (error) {
        alert('Terjadi kesalahan: ' + error.message);
        console.error(error);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Impor RPP';
    }
});
</script>
@endsection