namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::latest()->paginate(10);
        return view('backend.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('backend.areas.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Area::create($request->all());

        return redirect()->route('areas.index')
            ->with('success', 'Area berhasil ditambahkan');
    }

    public function edit($id)
    {
        $area = Area::findOrFail($id);
        return view('backend.areas.form', compact('area'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $area = Area::findOrFail($id);
        $area->update($request->all());

        return redirect()->route('areas.index')
            ->with('success', 'Area berhasil diupdate');
    }

    public function destroy($id)
    {
        Area::destroy($id);

        return redirect()->route('areas.index')
            ->with('success', 'Area berhasil dihapus');
    }
}
