<?php
require_once("Scrapper.php");
require_once("Mangapark.php");
require_once("Comunicacion.php");
require_once("InsertarMangas.php");
require_once("Lightnovelworld.php");
require_once("InsertarNovelas.php");
switch ($argv[1]) {

    case ("manga"):
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
                $novelInfo = $lightNovelWorld->getSeriesInfo(false, $argv[3]);
                echo "Info obtained" . PHP_EOL;
                echo "getting chapters..." . PHP_EOL;
                $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo);
                echo "------capitulos: " . count($novelWithChapters["chapters"]) . PHP_EOL;
                echo "Inserting Novel" . PHP_EOL;

                $novelID = $insert->insertNovel($novelWithChapters);

                if (!$novelID) {
                    $novelID = $insert->getId($novelInfo["name"]);
                }
                echo "Inserting Chapters" . PHP_EOL;

                $insert->insertChapters($novelWithChapters["chapters"], $novelID);
                break;
            case ("all"):
                if ($argv[3]) {
                    $pages = $lightNovelWorld->getAllNovels($argv[3]);
                    foreach ($pages as $page) {
                        foreach ($page as $title => $novelURL) {
                            echo "starting with: " . $title . PHP_EOL;
                            $novelInfo = $lightNovelWorld->getSeriesInfo(false, $novelURL);
                            echo "obtained basic info" . PHP_EOL;
                            $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo);
                            echo "obtained chapters" . PHP_EOL;
                            echo "------capitulos: " . count($novelWithChapters["chapters"]) . PHP_EOL;
                            $novelID = $insert->insertNovel($novelWithChapters);
                            if (!$novelID) {
                                $novelID = $insert->getId($novelInfo["name"]);
                            }
                            echo "novel inserted" . PHP_EOL;
                            $insert->insertChapters($novelWithChapters["chapters"], $novelID);
                            echo "chapters inserted" . PHP_EOL;
                        }
                    }
                } else {
                    $pages = $lightNovelWorld->getAllNovels();
                    foreach ($pages as $page) {
                        foreach ($page as $title => $novelURL) {
                            echo "starting with: " . $title . PHP_EOL;
                            $novelInfo = $lightNovelWorld->getSeriesInfo(false, $novelURL);
                            echo "obtained basic info" . PHP_EOL;
                            $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo);
                            echo "obtained chapters" . PHP_EOL;
                            echo "------capitulos: " . count($novelWithChapters["chapters"]) . PHP_EOL;
                            $novelID = $insert->insertNovel($novelWithChapters);
                            if (!$novelID) {
                                $novelID = $insert->getId($novelInfo["name"]);
                            }
                            echo "novel inserted" . PHP_EOL;
                            $insert->insertChapters($novelWithChapters["chapters"], $novelID);
                            echo "chapters inserted" . PHP_EOL;
                        }
                    }
                }
                break;
            default:
                echo "wrong arguments" . PHP_EOL;
                break;
        }
        //Obtener un capitulo
        //echo $lightNovelWorld->getChapter("https://www.lightnovelworld.com/novel/trash-of-the-counts-family-web-novel/chapter-3");

        //Obtener todos los capitulos con limite
        //var_dump($lightNovelWorld->getAllChapters($lightNovelWorld->getSeriesInfo("under the oak tree"), 3));
        //$lightNovelWorld->getSeriesInfo("under the oak tree");
        //var_dump($lightNovelWorld->getAllGenres());

        //Obtener una novela y sus capitulos
        /* $novelInfo = $lightNovelWorld->getSeriesInfo(false, "https://www.lightnovelworld.com/novel/the-kings-avatar-for-the-glory");
    $insert = new InsertarNovelas(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
    $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo);
    echo "------capitulos: " . count($novelWithChapters["chapters"]) . PHP_EOL;
    $novelID = $insert->insertNovel($novelWithChapters);
    if (!$novelID) {
        $novelID = $insert->getId($novelInfo["name"]);
    }
    $insert->insertChapters($novelWithChapters["chapters"], $novelID); */

        //Obtener todas las novelas y sus capitulos con limite de pagina;
        /* $insert = new InsertarNovelas(["DSN" => "mysql:host=localhost;dbname=laravelApp", "USER" => "sergio", "PASSWORD" => "sergiio666"]);
        $pages = $lightNovelWorld->getAllNovels(1);
        foreach ($pages as $page) {
            foreach ($page as $title => $novelURL) {
                echo "starting with: " . $title . PHP_EOL;
                $novelInfo = $lightNovelWorld->getSeriesInfo(false, $novelURL);
                echo "obtained basic info" . PHP_EOL;
                $novelWithChapters = $lightNovelWorld->getAllChapters($novelInfo);
                echo "obtained chapters" . PHP_EOL;
                echo "------capitulos: " . count($novelWithChapters["chapters"]) . PHP_EOL;
                $novelID = $insert->insertNovel($novelWithChapters);
                if (!$novelID) {
                    $novelID = $insert->getId($novelInfo["name"]);
                }
                echo "novel inserted" . PHP_EOL;
                $insert->insertChapters($novelWithChapters["chapters"], $novelID);
                echo "chapters inserted" . PHP_EOL;
            }
        } */
        break;
    default:
        echo "Wrong arguments" . PHP_EOL;
        break;
}
