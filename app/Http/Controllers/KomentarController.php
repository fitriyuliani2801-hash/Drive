public function index()
{
    // 1. Ambil data dari tabel
    $comments = DB::table('comments')->get();
    
    // 2. Kirim data ke file view (misalnya 'comments.blade.php')
    return view('comments', ['comments' => $comments]); 
}