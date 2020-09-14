<?php

namespace App;

use App\Scrapper;
use App\libs\fpdf182\fpdf;

class Mangapark extends scrapper
{
    public $baseURL1;
    public $baseURL2;
    public $baseURL3;
    public $seriesInfo;
    public $baseDownloadDir;
    public function __construct($baseDownloadDir)
    {
        parent::__construct();
        $this->baseURL1 = "https://mangapark.net/manga/";
        $this->baseURL2 = "?st-mcl=3";
        $this->baseURL3 = "https://mangapark.net/search?genres=";
        $this->baseDownloadDir = $baseDownloadDir;
    }

    public function getSeriesInfo($seriesName, $options = [])
    {
        $seriesName = str_replace(" ", "-", trim($seriesName));
        $seriesInfo = [];
        $html = $this->wget($this->baseURL1 . $seriesName . $this->baseURL2);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $seriesInfo["name"] = $seriesName;
        $imagenURL = $xpath->query("/html/body/section/div/div[2]/div[1]/div/img")->item(0);
        if ($imagenURL) {
            $imagenURL = $imagenURL->getAttribute("src");
        }
        if ($options["withImagen"]) {
            $imageInfo = base64_encode($this->wget("https:" . $imagenURL));
            $seriesInfo["imageInfo"] = $imageInfo;
        }
        $seriesInfo["imagenURL"] = "https:" . $imagenURL;
        $otherInfoContainers = $xpath->query("/html/body/section/div/div[2]/div[2]/table/tr[position() != last() and position() != 1]");
        foreach ($otherInfoContainers as $otherInfoContainer) {
            $toReplace = ["\n", "\t"];
            $title = str_replace($toReplace, "", $xpath->query("./th", $otherInfoContainer)->item(0)->nodeValue);
            $content = str_replace($toReplace, "", $xpath->query("./td", $otherInfoContainer)->item(0)->nodeValue);
            $seriesInfo["otherInfo"][$title] = $content;
        }
        $synopsis = $xpath->query("/html/body/section/div/p")->item(0);
        if ($synopsis) {
            $seriesInfo["synopsis"] = $synopsis->nodeValue;
        }
        $versions = $xpath->query("//*[@id=\"list\"]/div");
        foreach ($versions as $version) {
            $versionHeaders = $xpath->query("./div[1]/div[1]//span", $version);
            $versionName = $versionHeaders->item(0)->nodeValue;
            $versionInfo = $versionHeaders->item(1)->nodeValue;
            $seriesInfo["versions"][$versionName]["versionInfo"] = $versionInfo;
            $versionChapters = $xpath->query("./div[position()!=1]", $version);
            foreach ($versionChapters as $versionChapter) {
                $versionChapterContainers = $xpath->query("./div/div/div/div/div/a", $versionChapter);
                foreach ($versionChapterContainers as $versionChapterContainer) {
                    $chapterURL =  $versionChapterContainer->getAttribute("href");
                    $chapterURLbits = explode("/", $chapterURL);
                    unset($chapterURLbits[count($chapterURLbits) - 1]);
                    $chapterURLtrimed = "https://mangapark.net";
                    foreach ($chapterURLbits as $chapterURLbit) $chapterURLtrimed .= "/" . $chapterURLbit;
                    $chapterName =  $versionChapterContainer->nodeValue;
                    $seriesInfo["versions"][$versionName]["chapters"][$chapterName] = $chapterURLtrimed;
                }
            }
        }
        $this->seriesInfo = $seriesInfo;
        return $seriesInfo;
    }

    public function downloadVersions($versionsChapters, $user, $seriesName)
    {
        $downloadDir = $this->baseDownloadDir . "/" . $user->id . "/" . $seriesName;
        if (!file_exists($downloadDir)) {
            mkdir($downloadDir, 755, true);
        }

        foreach ($versionsChapters as $version => $versionChapters) {
            $downloadDirVersion = $downloadDir . "/" . $version;
            if (!file_exists($downloadDirVersion)) {
                mkdir($downloadDirVersion);
            }
            foreach ($versionChapters as $chapterTitle => $chapter) {
                $this->getChapter($chapter, $chapterTitle, $downloadDirVersion);
            }
        }
        $this->zipChapters($versionsChapters, $user, $seriesName);
        /* header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $this->seriesInfo["name"] . ".zip" . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize('/var/www/datas/' . $this->seriesInfo["name"] . ".zip"));
        ob_end_flush();
        @readfile('/var/www/datas/' . $this->seriesInfo["name"] . ".zip");
        unlink('/var/www/datas/' . $this->seriesInfo["name"] . ".zip");
        unlink('/var/www/datas/' . $this->seriesInfo["name"]); */
    }

    public function zipChapters($versionChapters, $user, $seriesName)
    {
        $zip = new \ZipArchive;
        $dir = $this->baseDownloadDir . "/" . $user->id . "/" . $seriesName . ".zip";
        if ($zip->open($dir, \ZipArchive::CREATE) === TRUE) {
            foreach ($versionChapters as $version => $chapters) {
                $zip->addEmptyDir($version);
                if ($handle = opendir($this->baseDownloadDir . "/" . $user->id . "/" . $seriesName . "/" . $version)) {
                    // Add all files inside the directory
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {
                            $zip->addFile($this->baseDownloadDir . "/" . $user->id . "/" . $seriesName . "/" . $version . "/" . $entry, $version . "/" . $entry);
                        }
                    }
                    closedir($handle);
                }
            }
            $zip->close();
        }
    }

    public function getChapter($url, $title, $downloadDirVersion)
    {
        $pdf = new FPDF();
        $html = $this->wget($url);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $images = $xpath->query("/html/body/script[2]");
        $resultado = explode('"u":', $images->item(0)->nodeValue);
        unset($resultado[0]);
        $final = [];
        foreach ($resultado as $trozo) {
            $trozos2 = explode('}', $trozo);
            $final[] = json_decode($trozos2[0]);
        }
        foreach ($final as $image) {
            $pdf->AddPage();
            $pdf->Image($image, 15, 15, 175, 275, 'JPG');
        }
        $pdf->Output("F", $downloadDirVersion . "/" . $title . ".pdf");
    }

    public function getAllMangasByGenre($genresIncluded, $genresExcluded)
    {
        //prepare URL to search
        $urlFinal = $this->baseURL3;
        foreach ($genresIncluded as $genre) {
            $urlFinal .= $genre . ",";
        }
        substr($urlFinal, 0, -1);
        $urlFinal .= "&genres-exclude=";
        foreach ($genresExcluded as $genre) {
            $urlFinal .= $genre . ",";
        }
        substr($urlFinal, 0, -1);
        $html = $this->wget($urlFinal);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $pagesLength = $xpath->query("/html/body/section/div/div[3]/div[1]/ul/li[3]/select/option")->length;
        //iterar sobre las paginas que contienen los mangas
        $seriesInfoByPage = [];
        for ($pageNumber = 1; $pageNumber <= $pagesLength; $pageNumber++) {
            $result = $this->getAllMangasInPageByGenre($urlFinal . "&page=" . $pageNumber);
            if ($result) {
                $seriesInfoByPage[] = $result;
            }
        }
        // ob_flush();
        // ob_start();
        // file_put_contents("dump.txt", ob_get_flush());
        return $seriesInfoByPage;
    }
    public function getAllMangasInPageByGenre($url)
    {
        $baseURL = "https://mangapark.net";
        $html = $this->wget($url);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $mangaLinks = $xpath->query("/html/body/section/div/div[3]/div[@class='item']//a[@class='cover']");
        $seriesInfoTotal = [];
        foreach ($mangaLinks as $mangaLink) {
            $link = $mangaLink->getAttribute("href");
            $seriesName = explode("/", $link);
            $seriesName = $seriesName[count($seriesName) - 1];
            $seriesInfoTotal[] = $this->getSeriesInfo($seriesName, ["withImagen" => true]);
        }
        return $seriesInfoTotal;
    }
    public function getAllGenres()
    {
        $url = "https://mangapark.net/search";
        $html = $this->wget($url);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $genres = $xpath->query("/html/body/section/div/div[2]/form/table/tbody[2]/tr[2]/td/ul/li/span");
        $genresParsed = [];
        foreach ($genres as $genre) {
            $genresParsed[] = str_replace(" ", "-", strtolower($genre->nodeValue));
        }
        return $genresParsed;
    }
}
