<?php


namespace App;

use App\Scrapper;


class ReadLightNovelScrapper extends Scrapper
{
    public $baseURL;
    public function __construct()
    {
        parent::__construct();
        $this->baseURL = "https://www.readlightnovel.org/";
    }

    function getSeriesInfo($seriesName)
    {
        $html = $this->wget($this->baseURL . $seriesName);
        $doc = new DOMDocument("1.0", 'iso-8859-1');
        @$doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $lastChapter = $xpath->query("/html/body/div[2]/div/div/div[1]/div/div[2]/div/div[2]/div/div[6]/div[2]/ul/li[1]/a");
        if (!$lastChapter->item(0)) {
            $lastChapter = $xpath->query("/html/body/div[2]/div/div/div[1]/div/div[2]/div/div[2]/div/div[10]/div[2]/ul/li[1]");
        }
        $lastChapterNumber = explode(" ", $lastChapter->item(0)->nodeValue)[1];
        $descriptionParts = $xpath->query("/html/body/div[2]/div/div/div[1]/div/div[2]/div/div[2]/div/div[1]/div[2]/p/span");
        $synopsis = "";
        foreach ($descriptionParts as $description) {
            $synopsis .= $description->nodeValue . PHP_EOL;
        }
        $picURL = $xpath->query("/html/body/div[2]/div/div/div[1]/div/div[2]/div/div[1]/div[1]/a/img")[0]->getAttribute("src");
        $picData = base64_encode($this->wget($picURL));
        $otherInfo = [];
        $rawInfo = $xpath->query("/html/body/div[2]/div/div/div[1]/div/div[2]/div/div[1]/div[4]/div");
        foreach ($rawInfo as $info) {
            $title = $xpath->query(".//div[1]/h6", $info)->item(0)->nodeValue;
            $titleData = $xpath->query("./div[2]/ul/li", $info)->item(0)->nodeValue;
            $otherInfo[$title] = $titleData;
        }
        $seriesInfo = ["chapters" => $lastChapterNumber, "synopsis" => $synopsis, "imageURL" => $picURL, "image" => $picData, "other" => $otherInfo];
        return $seriesInfo;
    }

    public function getChapterDOM($seriesName, $number)
    {
        $html = $this->wget($this->baseURL . $seriesName . "/chapter-" . $number);
        $doc = new DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $content = $xpath->query("/html/body/div[2]/div/div/div[1]/div/div[4]/div[2]")->item(0);
        $smalls = $content->getElementsByTagName("small");
        for ($i = 0; $i < $smalls->length; $i++) {
            $node = $smalls->item($i);
            $node->parentNode->removeChild($node);
            $i--;
        }
        $divs = $content->getElementsByTagName("div");
        for ($i = 0; $i < $divs->length; $i++) {
            $node = $divs->item($i);
            $node->parentNode->removeChild($node);
            $i--;
        }
        $scripts = $content->getElementsByTagName("scripts");
        for ($i = 0; $i < $scripts->length; $i++) {
            $node = $scripts->item($i);
            $node->parentNode->removeChild($node);
            $i--;
        }
        return $this->textDigestor($content->textContent);
    }

    public function textDigestor($text)
    {
        $arrayPieces1 = explode('“', $text);
        $arrayPieces2 = explode('"', $text);
        $arrayPiecesFinal = count($arrayPieces1) > count($arrayPieces2) ? $arrayPieces1 : $arrayPieces2;
        $finalText = "";
        for ($i = 0; $i < count($arrayPiecesFinal); $i++) {
            $finalText .= ($i % 2 === 0) ? $arrayPiecesFinal[$i] : "\n" . "”" . $arrayPiecesFinal[$i] . "”\n";
        }
        return $finalText;
    }
    public function getAllChapters($seriesName, $first = 1, $last)
    {
        require_once('./resources/fpdf.php');
        $pdf = new FPDF();
        for ($i = $first; $i <= $last; $i++) {
            $text = $this->getChapterDOM($seriesName, $i);
            $find = array('â€œ', 'â€™', 'â€¦', 'â€”', 'â€“', 'â€˜', 'Ã©', 'Â', 'â€¢', 'Ëœ', 'â€'); // en dash
            $replace = array('“', '’', '…', '—', '–', '‘', 'é', '', '•', '˜', '”');
            $text = str_replace($find, $replace, $text);
            /*if(!file_exists("/var/www/datas/".$seriesName)) {
                mkdir("/var/www/datas/".$seriesName,0777);
            }*/
            $pdf->AddPage();
            $pdf->SetFont('Times', "", 12);
            $pdf->MultiCell(0, 5, $text);
            $pdf->Ln();
            //file_put_contents("/var/www/datas/".$seriesName."/".$seriesName."-".$i,$text);
        }
        $pdf->Output();
    }
    public function cambiarCaracteres($text)
    {
        $text = preg_replace('â€¦', "...", $text);
        $text = preg_replace('â€™', "’", $text);
        $text = preg_replace('â€œ', "“", $text);
        $text = preg_replace("/â€[[:cntrl:]]/", "”", $text);
        return $text;
    }

    public function addToPdf($name, $text)
    {
    }
}
