<?php
class InsertarNovelas extends Comunicacion
{
    public $stmtInsertGenres;
    public function __construct($credenciales)
    {
        parent::__construct($credenciales);
        $this->stmtInsertGenres = $this->oConn->prepare("INSERT INTO genres (genre) VALUES(?)");
        $this->stmtSeriesInserted = $this->oConn->prepare("SELECT novels.*,count(novels.id) as chapters from novels left join novel_chapters on novels.id=novel_chapters.novel_id where name=? group by novels.name");
    }
    public function insertGenres($genres)
    {
        foreach ($genres as $genre) {
            try {
                $this->stmtInsertGenres->execute([$genre]);
            } catch (PDOException $error) {
                echo $error->getMessage() . PHP_EOL;
            }
        }
    }
    public function getSeriesInserted($name)
    {
        try {
            $this->stmtSeriesInserted->execute([$name]);
            $results = $this->stmtSeriesInserted->fetchAll();
            if (!$results) {
                return null;
            }
            return $results[0];
        } catch (PDOException $error) {
            echo $error->getMessage();
        }
    }

    public function insertNovel($novel)
    {
        $sqlInsertarPagina = "INSERT INTO `novels` (`name`,`author`,`imageInfo`,`alternativeTitle`,`status`,`genre`,`synopsis`) VALUES (?,?,?,?,?,?,?)";
        $dataToInsert = [];
        $dataToInsert[] = $novel["name"];
        $dataToInsert[] = $novel["otherInfo"]["author"];
        $dataToInsert[] = $novel["imageInfo"];
        $dataToInsert[] = $novel["otherInfo"]["alternativeTitle"];
        $dataToInsert[] = $novel["otherInfo"]["status"];
        $dataToInsert[] = implode(", ", $novel["otherInfo"]["genre"]);
        $dataToInsert[] = $novel["synopsis"];

        $stmtInsertarPagina = $this->oConn->prepare($sqlInsertarPagina);
        try {
            $stmtInsertarPagina->execute($dataToInsert);
            return $this->oConn->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    public function insertChapters($chapters, $id, $lastNumberInserted = 0)
    {
        $sqlInsertarChapter = "INSERT INTO `novel_chapters` (`number`,`title`,`content`,`novel_id`) VALUES ";
        $sqlInsertarHistory = "INSERT INTO `novels_update_history` (`chapters_introduced`,`novel_id`) VALUES(?,?)";
        $dataToInsert = [];
        $chaptersInserted = [];
        foreach ($chapters as $number => $chapter) {
            $sqlInsertarChapter .= "(?,?,?,?),";
            $dataToInsert[] = $lastNumberInserted + $number;
            $chaptersInserted[] = $lastNumberInserted + $number;
            $dataToInsert[] = isset($chapter["title"]) ? $chapter["title"] : ("no title" . ($lastNumberInserted + $number + 1));
            $dataToInsert[] = $chapter["content"];
            $dataToInsert[] = $id;
        }
        $sqlInsertarChapter = substr($sqlInsertarChapter, 0, -1);
        $stmtInsertarChapter = $this->oConn->prepare($sqlInsertarChapter);
        $stmtInsertarHistory = $this->oConn->prepare($sqlInsertarHistory);
        try {
            $stmtInsertarChapter->execute($dataToInsert);
            $stmtInsertarHistory->execute([json_encode($chaptersInserted), $id]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function getId($name)
    {
        $stmtGetId = $this->oConn->prepare("SELECT id FROM novels where name=?");
        try {
            $stmtGetId->execute([$name]);
            return $stmtGetId->fetchAll()[0]["id"];
        } catch (PDOException $error) {
            echo $error->getMessage();
        }
    }
    public function getLastChapter($name)
    {
        $stmtGetLastChapter = $this->oConn->prepare("select count(novels.id) as capitulos from novels join novel_chapters on novels.id = novel_chapters.novel_id where novels.name=?");
        try {
            $stmtGetLastChapter->execute([$name]);
            $chapters = $stmtGetLastChapter->fetchAll();
            if (!$chapters) {
                return 0;
            }
            return $chapters[0]["capitulos"];
        } catch (PDOException $error) {
            echo $error->getMessage();
        }
    }
}
