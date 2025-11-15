@extends('layouts.admin')

@section('title', 'Report Presenze')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-user-check text-fucsia-magia mr-3"></i>
                Report Presenze Dettagliato
            </h1>
            <p class="text-gray-600 mt-1">Registro completo presenze e assenze</p>
        </div>
        <a href="{{ route('admin.report.index', ['data_inizio' => $dataInizio, 'data_fine' => $dataFine]) }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna ai Report
        </a>
    </div>

    <!-- Export Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 flex justify-between items-center">
        <div class="text-gray-600">
            Periodo: <strong>{{ \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($dataFine)->format('d/m/Y') }}</strong>
        </div>
        <a href="{{ route('admin.report.export-csv', ['tipo' => 'presenze', 'data_inizio' => $dataInizio, 'data_fine' => $dataFine]) }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
            <i class="fas fa-file-csv mr-2"></i>
            Export CSV
        </a>
    </div>

    <!-- Tabella Presenze -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Lezione
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cliente
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Stato
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check-in
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check-out
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($presenze as $presenza)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($presenza->data)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $presenza->lezione_titolo }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $presenza->nome }} {{ $presenza->cognome }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($presenza->stato === 'presente') bg-green-100 text-green-800
                            @elseif($presenza->stato === 'assente') bg-red-100 text-red-800
                            @elseif($presenza->stato === 'prenotato') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($presenza->stato) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $presenza->check_in ? \Carbon\Carbon::parse($presenza->check_in)->format('H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $presenza->check_out ? \Carbon\Carbon::parse($presenza->check_out)->format('H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                        <p>Nessuna presenza registrata nel periodo selezionato</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginazione -->
    @if($presenze->hasPages())
    <div class="mt-6">
        {{ $presenze->links() }}
    </div>
    @endif
</div>
@endsection
