<?php

use App\libs\fpdf182\fpdf;

class Lightnovelworld extends Scrapper
{
    public $baseURL1 = "https://www.lightnovelworld.com/novel/";
    public $baseURL2 = "https://www.lightnovelworld.com";
    public function __construct()
    {
        parent::__construct();
    }
    public function getSeriesInfo($name = false, $url = false)
    {
        $searchParam = $url ?  $url : $this->baseURL1 . str_replace(" ", "-", trim($name));
        $html = $this->wget($searchParam);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $seriesInfo["name"] = $name ? $name : explode("/", $url)[4];
        $image = $xpath->query("/html/body/main/article/header/div/div[1]/figure/img")->item(0);
        if (!is_null($image)) {
            $seriesInfo["imageInfo"] = base64_encode($this->wget($image->getAttribute("src")));
        }
        $seriesInfo["otherInfo"] = [];
        $alternativeTitle = $xpath->query("/html/body/main/article/header/div/div[2]/div[1]/h2")->item(0);
        if (!is_null($alternativeTitle)) {
            $seriesInfo["otherInfo"]["alternativeTitle"] = $alternativeTitle->nodeValue;
        }
        $author = $xpath->query("/html/body/main/article/header/div/div[2]/div[1]/div[1]/a/span")->item(0);
        if (!is_null($author)) {
            $seriesInfo["otherInfo"]["author"] = $author->nodeValue;
        }
        $status = $xpath->query("/html/body/main/article/header/div/div[2]/div[2]/span[4]/strong")->item(0);
        if (!is_null($status)) {
            $seriesInfo["otherInfo"]["status"] = $status->nodeValue;
        }
        $synopsis = $xpath->query("/html/body/main/article/div[1]/section[1]/div[1]/p")->item(0);
        if (is_null($synopsis)) {
            $summary = $xpath->query("/html/body/main/article/div[1]/section[1]/div[1]/div")->item(0);
            if (!is_null($summary)) {
                $seriesInfo["synopsis"] = $summary->nodeValue;
            }
        } else {
            $seriesInfo["synopsis"] = $synopsis->nodeValue;
        }
        $genres = $xpath->query("/html/body/main/article/header/div/div[2]/div[4]/ul/li/a");
        foreach ($genres as $genre) {
            $seriesInfo["otherInfo"]["genre"][] = $genre->nodeValue;
        }
        $lastChapter = $xpath->query("/html/body/main/article/header/div/div[2]/div[2]/span[1]/strong")->item(0);
        if (!is_null($lastChapter)) {
            $seriesInfo["lastChapter"] = (int) $lastChapter->nodeValue;
        }
        return $seriesInfo;
    }

    public function getAllChapters($seriesInfo, $from = false, $to = false)
    {
        if ($to > $seriesInfo["lastChapter"]) {
            return "No hay tantos capítulos";
        }
        if (!$to) {
            $to = $seriesInfo["lastChapter"];
        }
        if (!$from) {
            $from = 1;
        }

        $seriesInfo["chapters"] = [];
        for ($i = $from; $i <= $to; $i++) {
            $chapterURL = $this->baseURL1 . $seriesInfo["name"] . "/chapter-" . $i;
            $seriesInfo["chapters"][] = $this->getChapter($chapterURL);
            echo "gotten chapter: " . $i . PHP_EOL;
        }
        return $seriesInfo;
    }
    public function getChapter($url)
    {
        do {
            $html = $this->wget($url);
            $doc = new \DOMDocument("1.0", "utf-8");
            @$doc->loadHTML($html);
            $xpath = new \DOMXPath($doc);
            $chapter = [];
            $chapterContent = "";
            $textGlobalContainer = $xpath->query("/html/body/main/article/section[1]/div[2]")->item(0);
            sleep(2);
        } while (is_null($textGlobalContainer));
        if (!is_null($textGlobalContainer)) {
            $textGlobalDivs = $xpath->query("./div", $textGlobalContainer);
            foreach ($textGlobalDivs as $div) {
                $textGlobalContainer->removeChild($div);
            }
            $textGlobalSpans = $xpath->query("./span", $textGlobalContainer);
            foreach ($textGlobalSpans as $span) {
                $textGlobalContainer->removeChild($span);
            }
            $chapterContent =  $textGlobalContainer->C14N();
            $patrones = ['<div class="chapter-content">', '</div>', '<br></br>', "<p>", '</p>', '<span>', '</span>', '<li>'];
            $sustituciones = ["", "", "\n\n", "", "\n\n", "", "", ""];
            $chapterContent =  str_replace($patrones, $sustituciones, $chapterContent);
        }
        $chapter["content"] = $chapterContent;
        $title = $xpath->query("/html/body/main/article/header/div/div/h2")->item(0);
        if (!is_null($title)) {
            $chapter["title"] = $title->nodeValue;
        }
        return $chapter;
    }
    public function getAllPages()
    {
    }
    public function getAllNovels($limit = false)
    {
        $page = 1;
        $allNovels = [];
        if (!$limit) {
            do {
                echo "----Starting Page: " . $page . PHP_EOL;
                $html = $this->wget($this->baseURL2 . "/browse/all/popular/all/" . $page);
                $doc = new \DOMDocument("1.0", "utf-8");
                sleep(10);
                @$doc->loadHTML($html);
                $xpath = new \DOMXPath($doc);
                $pageNovels = $xpath->query("/html/body/main/article/section/ul/li/a");
                foreach ($pageNovels as $novel) {
                    $title = $xpath->query("./h4", $novel)->item(0)->nodeValue;
                    echo "----Starting Novel: " . $title . PHP_EOL;
                    $href = $novel->getAttribute("href");
                    $novelURL = $this->baseURL2 . $href;
                    $allNovels[$page][$title] = $novelURL;
                }
                $page++;
            } while (!is_null($pageNovels->item(0)));
        } else {
            for ($i = $page; $i <= $limit; $i++) {
                echo "----Starting Page: " . $i . PHP_EOL;
                $html = $this->wget($this->baseURL2 . "/browse/all/popular/all/" . $i);
                $doc = new \DOMDocument("1.0", "utf-8");
                sleep(10);
                @$doc->loadHTML($html);
                $xpath = new \DOMXPath($doc);
                $pageNovels = $xpath->query("/html/body/main/article/section/ul/li/a");
                foreach ($pageNovels as $novel) {
                    $title = $xpath->query("./h4", $novel)->item(0)->nodeValue;
                    echo "----Starting Novel: " . $title . PHP_EOL;
                    $href = $novel->getAttribute("href");
                    $novelURL = $this->baseURL2 . $href;
                    $allNovels[$i][$title] = $novelURL;
                }
            }
        }
        return $allNovels;
    }
    public function getAllGenres()
    {
        $genres = [];
        $html = $this->wget($this->baseURL2 . "/browse");
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $categories = $xpath->query("/html/body/main/article/nav/div/ul/li/a");
        foreach ($categories as $category) {
            $genres[] = str_replace(" ", "-", strtolower(trim($category->nodeValue)));
        }
        return $genres;
    }
    public function createBook($chapters, $name, $downloadDir)
    {
        $pdf = new FPDF();
        foreach ($chapters as $chapter) {
            $content = str_replace("<p>", "", str_replace("</p>", "\n\n", $chapter->content));
            $pdf->AddPage();
            $pdf->setFont("Arial", "B", 14, "B", "C");
            $pdf->MultiCell(0, 5, $chapter->title ? $chapter->title : "chapter " . $chapter->number, "B", "C");
            $pdf->SetFont('Times', "", 12);
            $pdf->MultiCell(0, 5, $content, "B");
            $pdf->Ln();
        }
        $pdf->Output('F', $downloadDir . "/" . $name . ".pdf");
    }
}
