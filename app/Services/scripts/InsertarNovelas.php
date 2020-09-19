<?php
class InsertarNovelas extends Comunicacion
{
    public function __construct($credenciales)
    {
        parent::__construct($credenciales);
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
            return $id = $this->oConn->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    public function insertChapters($chapters, $id)
    {
        $sqlInsertarChapter = "INSERT INTO `novel_chapters` (`number`,`title`,`content`,`novel_id`) VALUES ";
        $dataToInsert = [];
        foreach ($chapters as $number => $chapter) {
            $sqlInsertarChapter .= "(?,?,?,?),";
            $dataToInsert[] = $number;
            $dataToInsert[] = $chapter["title"];
            $dataToInsert[] = $chapter["content"];
            $dataToInsert[] = $id;
        }
        $sqlInsertarChapter = substr($sqlInsertarChapter, 0, -1);
        $stmtInsertarChapter = $this->oConn->prepare($sqlInsertarChapter);
        try {
            $stmtInsertarChapter->execute($dataToInsert);
            echo "capitulos insertados." . PHP_EOL;
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
}
