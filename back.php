<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1); // Evita errores en la salida JSON

$config = json_decode(file_get_contents("config.json"), true);

if (!$config) {
    echo json_encode(["error" => "No se pudo leer config.json"]);
    exit;
}

$hostname = $config['hostname'];
$username = $config['username'];
$password = $config['password'];

function fetch_emails($mailbox, $folder, &$uniqueEmails) {
    $correos = [];

    $inbox = @imap_open($mailbox . $folder, $GLOBALS['username'], $GLOBALS['password']);
    
    if (!$inbox) {
        error_log("Error conectando a la carpeta $folder: " . imap_last_error());
        return [];
    }

    $emails = imap_search($inbox, 'ALL');
    
    if ($emails) {
        foreach ($emails as $email_number) {
            $cabecera = imap_headerinfo($inbox, $email_number);
            $asunto = isset($cabecera->subject) ? imap_utf8($cabecera->subject) : "(Sin Asunto)";
            
            $from = isset($cabecera->from[0]->mailbox, $cabecera->from[0]->host) ? 
                    $cabecera->from[0]->mailbox . "@" . $cabecera->from[0]->host : "(Remitente desconocido)";
            
            $to = isset($cabecera->to[0]->mailbox, $cabecera->to[0]->host) ? 
                  $cabecera->to[0]->mailbox . "@" . $cabecera->to[0]->host : "(Destinatario desconocido)";

            $fecha = isset($cabecera->date) ? date("Y-m-d H:i:s", strtotime($cabecera->date)) : "(Fecha desconocida)";
            
            if ($from !== "info@freire-sanchez-valencia.es" && $from !== "(Remitente desconocido)") {
                $uniqueEmails[$from] = true;
            }
            if ($to !== "info@freire-sanchez-valencia.es" && $to !== "(Destinatario desconocido)") {
                $uniqueEmails[$to] = true;
            }

            $correos[] = [
                "carpeta" => $folder,
                "from" => $from,
                "to" => $to,
                "asunto" => $asunto,
                "fecha" => $fecha
            ];
        }
    }

    imap_close($inbox);
    return $correos;
}

$supercorreos = [];
$uniqueEmails = [];

$folders = ["INBOX", "Sent", "Sent Items", "Elementos enviados"];
foreach ($folders as $folder) {
    $emails = fetch_emails($hostname, $folder, $uniqueEmails);
    if (!empty($emails)) {
        $supercorreos = array_merge($supercorreos, $emails);
        break;
    }
}

$distinctEmails = array_keys($uniqueEmails);

$output = [
    "emails" => $supercorreos,
    "unique_contacts" => $distinctEmails
];

echo json_encode($output, JSON_PRETTY_PRINT);
exit; // Asegura que no haya más salida después del JSON

?>


