<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scheda di Allenamento - {$scheda->getNome_scheda()}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            border-bottom: 3px solid #AFAFE2;
            padding-bottom: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #4B3F72;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .info-card {
            background-color: #F4F9F1;
            border: 1px solid #AFAFE2;
            border-radius: 8px;
            padding: 12px;
        }
        .info-card strong {
            color: #4B3F72;
        }
        .session-box {
            border: 2px solid #99CDEA;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .session-title {
            margin-top: 0;
            color: #4B3F72;
            border-bottom: 1px solid #99CDEA;
            padding-bottom: 8px;
            font-size: 20px;
        }
        .session-desc {
            font-style: italic;
            color: #555;
            font-size: 13px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        th {
            background-color: #F4F9F1;
            color: #4B3F72;
            font-weight: bold;
        }
        .print-btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn {
            background-color: #AFAFE2;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            text-transform: uppercase;
        }
        @media print {
            .print-btn-container {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button class="print-btn" onclick="window.print()">Stampa / Salva in PDF</button>
    </div>

    <div class="header">
        <h1>GymFly - Scheda di Allenamento</h1>
        <p>Generata per l'atleta il {$smarty.now|date_format:"%d/%m/%Y"}</p>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <p>Atleta: <strong>{$utente->getNome()} {$utente->getCognome()}</strong></p>
            <p>Codice Fiscale: <strong>{$utente->getCF()}</strong></p>
            <p>Email: <strong>{$utente->getEmail()}</strong></p>
        </div>
        <div class="info-card">
            <p>Nome Scheda: <strong>{$scheda->getNome_scheda()}</strong></p>
            <p>Obiettivo: <strong>{$scheda->getObiettivo()}</strong></p>
            <p>Coach: <strong>{$scheda->getAllenatore()->getNome()} {$scheda->getAllenatore()->getCognome()}</strong></p>
            <p>Periodo: dal <strong>{$scheda->getData_inizio()->format('d/m/Y')}</strong> al <strong>{$scheda->getData_fine()->format('d/m/Y')}</strong></p>
        </div>
    </div>

    {foreach $scheda->getAllenamenti() as $allenamento}
        <div class="session-box">
            <h2 class="session-title">{$allenamento->getNome()}</h2>
            {if $allenamento->getDescrizione()}
                <p class="session-desc">{$allenamento->getDescrizione()|pulisci_descrizione}</p>
            {/if}

            <table>
                <thead>
                    <tr>
                        <th>Esercizio</th>
                        <th>Serie</th>
                        <th>Ripetizioni</th>
                        <th>Carico (Kg)</th>
                        <th>Tempo di Recupero / Note</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $allenamento->getDettagli() as $dettaglio}
                        <tr>
                            <td><strong>{$dettaglio->getEsercizio()->getNomeEsercizio()}</strong></td>
                            <td>{$dettaglio->getSerie()}</td>
                            <td>{$dettaglio->getRipetizioni()}</td>
                            <td>{$dettaglio->getCarico()} Kg</td>
                            <td>
                                {$allenamento->getDescrizione()|estrai_recupero:$dettaglio->getEsercizio()->getNomeEsercizio()}
                            </td>
                        </tr>
                    {foreachelse}
                        <tr>
                            <td colspan="5">Nessun esercizio presente in questa sessione.</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    {foreachelse}
        <p style="text-align: center; color: #777;">Nessun allenamento presente in questa scheda.</p>
    {/foreach}

    <script>
        // Avvia la stampa automatica dopo che la pagina è caricata
        window.addEventListener('DOMContentLoaded', () => {
            // Un breve ritardo garantisce il caricamento completo del layout prima di aprire la stampa del browser
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
