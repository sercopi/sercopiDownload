<?php
require_once("Scrapper.php");
require_once("Mangapark.php");
require_once("Comunicacion.php");
require_once("InsertarMangas.php");
require_once("Lightnovelworld.php");
require_once("InsertarNovelas.php");
/*$manga = new Mangapark();
$insertar = new InsertarManga(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
$genres = ["zombies", "romance", "historical", "adventure", "webtoon", "action", "isekai", "drama", "slice-of-life", "school-life"];
foreach ($genres as $genre) {
    $resultados = $manga->getAllMangasByGenre([$genre], []);
    echo "----------" . PHP_EOL;
    echo "insertando todos los del Género: " . $genre . PHP_EOL;
    foreach ($resultados as $numeroPagina => $pagina) {
        foreach ($pagina as $mangaInfo) {
            $datos = [
                "name" => $mangaInfo["name"],
                "imageInfo" => $mangaInfo["imageInfo"],
                "alternativeTitle" => $mangaInfo["otherInfo"]["Alternative"],
                "author" => $mangaInfo["otherInfo"]["Author(s)"],
                "artist" => $mangaInfo["otherInfo"]["Artist(s)"],
                "genre" => $mangaInfo["otherInfo"]["Genre(s)"],
                "type" => $mangaInfo["otherInfo"]["Type"],
                "status" => $mangaInfo["otherInfo"]["Status"],
                "synopsis" => $mangaInfo["synopsis"],
                "chapters" => json_encode($mangaInfo["versions"]),
            ];
            $insertar->insertar($datos);
            echo "insertado: " . $mangaInfo["name"] . PHP_EOL;
        }
        echo "-----------" . PHP_EOL;
        echo "insertados los de pagina: " . $numeroPagina . PHP_EOL;
    }
    echo "----------" . PHP_EOL;
    echo "insetados todos los del genero: " . $genre . PHP_EOL;
}
*/
$lightNovelWorld = new Lightnovelworld();
//echo $lightNovelWorld->getChapter("https://www.lightnovelworld.com/novel/trash-of-the-counts-family-web-novel/chapter-3");
//var_dump($lightNovelWorld->getAllChapters($lightNovelWorld->getSeriesInfo("under the oak tree"), 3));
//$lightNovelWorld->getSeriesInfo("under the oak tree");
//var_dump($lightNovelWorld->getAllGenres());
$novels = $lightNovelWorld->getAllNovels(1);
$insert = new InsertarNovelas(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
foreach ($novels as $novel) {
    $id = $insert->insertNovel($novel);
    $insert->insertChapters($novel["chapters"], $id);
}
