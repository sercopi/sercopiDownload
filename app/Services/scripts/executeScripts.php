<?php
require_once("Scrapper.php");
require_once("Mangapark.php");
require_once("Comunicacion.php");
require_once("InsertarMangas.php");
require_once("Lightnovelworld.php");
require_once("InsertarNovelas.php");
switch ($argv[1]) {
    case ("manga"):
        $manga = new Mangapark("");
        $insertar = new InsertarManga(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
        switch ($argv[2]) {
            case ("manga"):
                $name = str_replace(" ", "-", $argv[3]);
                $seriesInfo = $manga->getSeriesInfo($name, ["withImagen" => true]);
                downloadManga($seriesInfo, $manga, $insertar);
                break;
            case ("all"):
                $genres = $manga->getAllGenres();
                foreach ($genres as $genre) {
                    echo "obteniendo todos los del Género: " . $genre . PHP_EOL;
                    $resultados = $manga->getAllMangasByGenre([$genre], []);
                    echo "----------" . PHP_EOL;
                    echo "insertando todos los del Género: " . $genre . PHP_EOL;
                    foreach ($resultados as $numeroPagina => $pagina) {
                        foreach ($pagina as $seriesInfo) {
                            downloadManga($seriesInfo, $manga, $insertar);
                        }
                        echo "-----------" . PHP_EOL;
                        echo "insertados los de pagina: " . $numeroPagina . PHP_EOL;
                    }
                    echo "----------" . PHP_EOL;
                    echo "insetados todos los del genero: " . $genre . PHP_EOL;
                }
                break;
        }
        break;
    case ("novel"):
        //php executeScripts tipoResource Modo AdditionalParams
        $lightNovelWorld = new Lightnovelworld();
        $insert = new InsertarNovelas(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);

        switch ($argv[2]) {
            case ("chapter"):
                var_dump($lightNovelWorld->getChapter($argv[3]));
                break;
            case ("novel"):
                //arg3 es la url
                downloadNovel($argv[3], $lightNovelWorld, $insert);
                break;
            case ("all"):
                echo "getting all novels from pages" . PHP_EOL;
                if (isset($argv[3])) {
                    $pages = $lightNovelWorld->getAllNovels($argv[3]);
                    foreach ($pages as $number => $page) {
                        echo "-------------------" . PHP_EOL;
                        echo "Starting inserts from page: " . $number . PHP_EOL;
                        foreach ($page as $title => $novelURL) {
                            echo "starting with: " . $title . PHP_EOL;
                            downloadNovel($novelURL, $lightNovelWorld, $insert);
                        }
                    }
                } else {
                    $pages = $lightNovelWorld->getAllNovels();
                    foreach ($pages as $number => $page) {
                        echo "-------------------" . PHP_EOL;
                        echo "Starting inserts from page: " . $number . PHP_EOL;
                        foreach ($page as $title => $novelURL) {
                            echo "starting with: " . $title . PHP_EOL;
                            downloadNovel($novelURL, $lightNovelWorld, $insert);
                        }
                    }
                }
                break;
            default:
                echo "wrong arguments" . PHP_EOL;
                break;
        }
        break;
    default:
        echo "Wrong arguments" . PHP_EOL;
        break;
}
function downloadNovel($url, $lightNovelWorld, $insert)
{
    $novelInfo = $lightNovelWorld->getSeriesInfo(false, $url);
    var_dump($novelInfo);
    echo "Info obtained" . PHP_EOL;
    isset($novelInfo["lastChapter"]) ? $novelInfo["lastChapter"] : $novelInfo["lastChapter"] = "unknown";
    echo "last chapter: " . $novelInfo["lastChapter"] . PHP_EOL;
    $lastInserted = $insert->getLastChapter($novelInfo["name"]);
    echo "last inserted: " . $lastInserted . PHP_EOL;
    if ($novelInfo["lastChapter"] == "unknown" || $lastInserted < $novelInfo["lastChapter"]) {
        echo "getting chapters..." . PHP_EOL;
        $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo, $lastInserted + 1, false);
        echo "------capitulos: " . count($novelWithChapters["chapters"]) . PHP_EOL;
        //si ya hay capitulos, no insertamos la novela
        $novelID = false;

        if (!$lastInserted) {
            echo "Inserting Novel" . PHP_EOL;
            $novelID = $insert->insertNovel($novelWithChapters);
        }
        if (!$novelID) {
            echo "Novel already Inserted" . PHP_EOL;
            $novelID = $insert->getId($novelInfo["name"]);
        }
        echo "Inserting Chapters" . PHP_EOL;

        $insert->insertChapters($novelWithChapters["chapters"], $novelID, $lastInserted);
        echo "capitulos insertados y registro actualizado." . PHP_EOL;
        echo "-----------------" . PHP_EOL;
    } else {
        echo "Novel already up to Date" . PHP_EOL;
    }
}
function downloadManga($seriesInfo, $manga, $insertar)
{

    //comprobamos si la serie estaba insertada
    $seriesInserted = $insertar->getSeriesInserted($seriesInfo["name"]);

    if (!is_null($seriesInserted)) {
        echo "-----serie insertada" . PHP_EOL;
        //la serie ya estaba insertada, se comprueban los capitulos
        if (json_encode($seriesInfo["versions"]) !== $seriesInserted["chapters"]) {
            //si es diferente, entonces se updatean sus capitulos y se guarda en el hsitorial
            echo "-------descargando cambios" . PHP_EOL;

            $insertar->updateChapters($seriesInfo["versions"], $seriesInserted["chapters"], $seriesInserted["id"]);
        } else {
            echo "-------sin cambios" . PHP_EOL;
        }
    } else {
        echo "-----serie no insertada, insertando..." . PHP_EOL;
        //si no estaba insertada, se inserta todo
        $datos = [
            "name" => $seriesInfo["name"],
            "imageInfo" => $seriesInfo["imageInfo"],
            "alternativeTitle" => $seriesInfo["otherInfo"]["Alternative"],
            "author" => $seriesInfo["otherInfo"]["Author(s)"],
            "artist" => $seriesInfo["otherInfo"]["Artist(s)"],
            "genre" => $seriesInfo["otherInfo"]["Genre(s)"],
            "type" => $seriesInfo["otherInfo"]["Type"],
            "status" => $seriesInfo["otherInfo"]["Status"],
            "synopsis" => $seriesInfo["synopsis"],
            "chapters" => json_encode($seriesInfo["versions"]),
        ];
        $insertar->insertar($datos);
        echo "insertado: " . $seriesInfo["name"] . PHP_EOL;
    }
}
