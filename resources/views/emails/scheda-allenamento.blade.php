@extends('emails.layout')

@section('contenuto')
<tr>
    <td style="padding: 40px 30px;">
        <!-- Intestazione -->
        <h1 style="color: #E91E8C; font-size: 28px; margin-bottom: 10px; font-weight: bold;">
            Nuova Scheda di Allenamento! 🎉
        </h1>
        <p style="color: #666; font-size: 16px; margin-bottom: 30px;">
            Ciao {{ $scheda->cliente->nome }}, la tua scheda personalizzata è pronta!
        </p>

        <!-- Informazioni Scheda -->
        <div style="background: #f8f9fa; border-left: 4px solid #E91E8C; padding: 20px; margin-bottom: 30px; border-radius: 4px;">
            <h2 style="color: #333; font-size: 20px; margin-bottom: 15px; font-weight: bold;">
                📋 {{ $scheda->nome_scheda }}
            </h2>

            @if($scheda->descrizione)
            <p style="color: #666; font-size: 15px; margin-bottom: 10px; line-height: 1.6;">
                <strong>Descrizione:</strong> {{ $scheda->descrizione }}
            </p>
            @endif

            @if($scheda->obiettivi)
            <p style="color: #666; font-size: 15px; margin-bottom: 10px; line-height: 1.6;">
                <strong>Obiettivi:</strong> {{ $scheda->obiettivi }}
            </p>
            @endif

            <!-- Statistiche -->
            <table style="width: 100%; margin-top: 20px;">
                <tr>
                    <td style="padding: 10px; background: white; border-radius: 4px; text-align: center; width: 33%;">
                        <div style="font-size: 24px; color: #E91E8C; font-weight: bold;">
                            {{ $scheda->numeroGiorniAllenamento() }}
                        </div>
                        <div style="font-size: 12px; color: #999; text-transform: uppercase; margin-top: 5px;">
                            Giorni/Settimana
                        </div>
                    </td>
                    <td style="width: 2%;"></td>
                    <td style="padding: 10px; background: white; border-radius: 4px; text-align: center; width: 33%;">
                        <div style="font-size: 24px; color: #E91E8C; font-weight: bold;">
                            {{ $scheda->numeroEserciziTotali() }}
                        </div>
                        <div style="font-size: 12px; color: #999; text-transform: uppercase; margin-top: 5px;">
                            Esercizi Totali
                        </div>
                    </td>
                    <td style="width: 2%;"></td>
                    <td style="padding: 10px; background: white; border-radius: 4px; text-align: center; width: 33%;">
                        <div style="font-size: 24px; color: #E91E8C; font-weight: bold;">
                            {{ $scheda->durata_settimane ?? '-' }}
                        </div>
                        <div style="font-size: 12px; color: #999; text-transform: uppercase; margin-top: 5px;">
                            Settimane
                        </div>
                    </td>
                </tr>
            </table>

            @if($scheda->data_inizio && $scheda->data_fine)
            <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 4px;">
                <p style="color: #666; font-size: 14px; margin: 0;">
                    <strong>Periodo:</strong>
                    Dal {{ $scheda->data_inizio->format('d/m/Y') }}
                    al {{ $scheda->data_fine->format('d/m/Y') }}
                </p>
            </div>
            @endif
        </div>

        <!-- Messaggio Motivazionale -->
        <div style="background: linear-gradient(135deg, #E91E8C 0%, #C41E6F 100%); color: white; padding: 20px; margin-bottom: 30px; border-radius: 4px; text-align: center;">
            <p style="font-size: 18px; margin: 0; line-height: 1.6;">
                💪 <strong>Sei pronta a raggiungere i tuoi obiettivi!</strong>
            </p>
            <p style="font-size: 15px; margin: 10px 0 0 0; opacity: 0.95;">
                La tua scheda personalizzata è in allegato. Buon allenamento!
            </p>
        </div>

        <!-- Consigli del Professionista -->
        @if($scheda->consigli_professionista)
        <div style="background: #fff8e1; border-left: 4px solid #ffc107; padding: 20px; margin-bottom: 30px; border-radius: 4px;">
            <h3 style="color: #856404; font-size: 16px; margin-bottom: 10px; font-weight: bold;">
                💡 Consigli del tuo Professionista
            </h3>
            <p style="color: #856404; font-size: 14px; margin: 0; line-height: 1.6;">
                {{ $scheda->consigli_professionista }}
            </p>
        </div>
        @endif

        @if($scheda->note_alimentazione)
        <div style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 20px; margin-bottom: 30px; border-radius: 4px;">
            <h3 style="color: #2e7d32; font-size: 16px; margin-bottom: 10px; font-weight: bold;">
                🥗 Consigli Alimentazione
            </h3>
            <p style="color: #2e7d32; font-size: 14px; margin: 0; line-height: 1.6;">
                {{ $scheda->note_alimentazione }}
            </p>
        </div>
        @endif

        <!-- PDF Allegato -->
        <div style="border: 2px dashed #ddd; padding: 20px; margin-bottom: 30px; border-radius: 4px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 10px;">📄</div>
            <p style="color: #333; font-size: 16px; margin: 0;">
                <strong>La tua scheda è allegata in formato PDF</strong>
            </p>
            <p style="color: #999; font-size: 14px; margin: 10px 0 0 0;">
                Puoi stamparla o salvarla sul tuo dispositivo
            </p>
        </div>

        <!-- Informazioni Professionista -->
        @if($scheda->professionista)
        <div style="background: #f1f8ff; border-left: 4px solid #0366d6; padding: 20px; margin-bottom: 30px; border-radius: 4px;">
            <h3 style="color: #0366d6; font-size: 16px; margin-bottom: 10px; font-weight: bold;">
                👤 Il tuo Professionista
            </h3>
            <p style="color: #333; font-size: 15px; margin: 0;">
                <strong>{{ $scheda->professionista->nome }} {{ $scheda->professionista->cognome }}</strong>
            </p>
            @if($scheda->professionista->email)
            <p style="color: #666; font-size: 14px; margin: 5px 0 0 0;">
                📧 {{ $scheda->professionista->email }}
            </p>
            @endif
        </div>
        @endif

        <!-- Call to Action -->
        <div style="text-align: center; margin: 40px 0;">
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                Hai domande sulla tua scheda? Contattaci!<br>
                Siamo sempre qui per supportarti nel tuo percorso.
            </p>
        </div>

        <!-- Firma -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p style="color: #999; font-size: 13px; margin: 0;">
                Un abbraccio,<br>
                <strong style="color: #E91E8C;">Il Team MA.GIA DONNA</strong>
            </p>
        </div>
    </td>
</tr>
@endsection
