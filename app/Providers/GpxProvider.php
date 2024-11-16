<?php

function gpxToMarkdown($gpxFile) {
    if (!file_exists($gpxFile)) {
        throw new Exception("GPX file not found: $gpxFile");
    }

    $xml = simplexml_load_file($gpxFile);
    if ($xml === false) {
        throw new Exception("Failed to parse GPX file");
    }

    $output = "";
    $pointId = 0;

    foreach ($xml->trk as $track) {
        foreach ($track->trkseg as $segment) {
            foreach ($segment->trkpt as $point) {
                $pointId++;
                $id = generateId();

                $output .= "  -\n";
                $output .= "    id: " . $id . "\n";
                $output .= "    key: lat\n";
                $output .= "    value: A\n";
                $output .= "    lat: '" . (float)$point['lat'] . "'\n";
                $output .= "    long: '" . (float)$point['lon'] . "'\n";

                if (isset($point->ele)) {
                    $output .= "    ele: '" . (float)$point->ele . "'\n";
                }

                if (isset($point->time)) {
                    $output .= "    time: '" . (string)$point->time . "'\n";
                }

                $output .= "\n";
            }
        }
    }

    return $output;
}

function generateId($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $id;
}

try {
    $gpxFile = $argv[1] ?? null;
    if (!$gpxFile) {
        throw new Exception("Please provide a GPX file path as argument");
    }

    $markdown = gpxToMarkdown($gpxFile);
    echo $markdown;

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}