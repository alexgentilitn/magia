<!-- Form Upload -->
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.pesate.process-import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Selezione Sede -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-map-marker-alt mr-1 text-fucsia-magia"></i>
                Sede <span class="text-red-500">*</span>
            </label>
            <select name="sede" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent">
                <option value="">Seleziona una sede...</option>
                <option value="Calliano">Calliano</option>
                <option value="Darè">Darè</option>
                <option value="Pieve di Bono">Pieve di Bono</option>
                <option value="Riva">Riva</option>
                <option value="Trento">Trento</option>
            </select>
            <p class="text-sm text-gray-500 mt-1">Seleziona la sede dove sono state effettuate le pesate</p>
        </div>

        <!-- Upload File -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-file-excel mr-1 text-green-600"></i>
                File Excel <span class="text-red-500">*</span>
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-fucsia-magia transition">
                <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                <input type="file" name="file" accept=".xlsx,.xls" required
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-fucsia-magia file:text-white
                              hover:file:bg-viola-magia
                              cursor-pointer">
                <p class="text-sm text-gray-500 mt-2">
                    Formati supportati: .xlsx, .xls (max 10 MB)
                </p>
            </div>
        </div>

        <!-- Bottoni -->
        <div class="flex gap-4">
            <button type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-viola-magia to-fucsia-magia text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-upload mr-2"></i>
                Carica e Visualizza Anteprima
            </button>
        </div>
    </form>
</div>
