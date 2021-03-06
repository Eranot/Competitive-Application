<?php
// Include the class that we create objects of
require_once("../models/m_contest.php");

// Begin scraping
scrape_atcoder();

function scrape_atcoder()
{
    // Pull data for all contests
    $site_data = file_get_contents("https://atcoder.jp/contest");

    // Error, website not found
    if (empty($site_data)) {
        echo json_encode("INV");

        return;
    }

    preg_match_all("/<tbody>([\s\S]*?)<\/tbody>/", $site_data, $site_data);
    $site_data = $site_data[0][0] . $site_data[0][1];

    // Check for errors, and grab webpage of individual competitions
    if (!preg_match_all("/<a href=\"(https:\/\/.*?\.contest\.atcoder\.jp)\"[^>]*>/", $site_data, $matches)) {
        echo json_encode("INV");

        return;
    }

    // Grab title and dates
    preg_match_all("/<a href=\"https:\/\/.*?\.contest\.atcoder\.jp\"[^>]*>(.*)<\/a>/", $site_data, $name);
    preg_match_all("/target=\"_blank\">([0-9]{4}\/[0-9]{2}\/[0-9]{2} [0-9]{2}:[0-9]{2})<\/a><\/td>/", $site_data, $dates_start);
    preg_match_all("/<td class=\"text-center\">([0-9]{2}:[0-9]{2})<\/td>/", $site_data, $dates_end);

    // Iterate through competitions
    for ($i = 0; $i < count($matches[1]); $i = $i + 1) {
        $match = $matches[1][$i];

        // Add details to array
        $arr[] = new contest(
            "AtCoder",
            "/images/AtCoder.png",
            $name[1][$i],
            strtotime($dates_start[1][$i]),
            '',
            "Click link to view details",
            $match
        );
    }

    // No details found!
    if (empty($arr)) {
        echo json_encode("INV");

        return;
    }

    // Output details
    echo json_encode($arr);
}
