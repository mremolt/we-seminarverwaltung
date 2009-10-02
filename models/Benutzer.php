<?php

namespace models;
use library\Database, library\ActiveRecord, library\DateTime;
use \PDO;

/**
 * Ein Benutzer
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package models
 */
class Benutzer extends ActiveRecord
{
    protected $vorname;
    protected $name;
    protected $email;
    protected $passwort;
    protected $registriert_seit;
    protected $anrede;

    /**
     * Getter 
     * 
     * @return string
     */
    public function getVorname()
    {
        return $this->vorname;
    }

    /**
     * Setter
     *
     * @param string $vorname
     * @return Benutzer
     */
    public function setVorname($vorname)
    {
        if (empty($vorname)) {
            $this->addError('vorname', 'Das Feld Vorname darf nich leer sein');
        }
        $this->vorname = $vorname;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter
     *
     * @param string $name
     * @return Benutzer
     */
    public function setName($name)
    {
        if (empty($name)) {
            $this->addError('name', 'Das Feld Name darf nich leer sein');
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Setter
     *
     * @param string $email
     * @return Benutzer
     */
    public function setEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            $this->addError('email', 'Sie haben keine gültige E-Mail-Adresse eingegeben.');
        }
        $this->email = $email;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getPasswort()
    {
        return $this->passwort;
    }

    /**
     * Setter
     *
     * @param string $passwort
     * @return Benutzer
     */
    public function setPasswort($passwort)
    {
        if (empty($passwort)) {
            $this->addError('passwort', 'Das Feld Passwort darf nich leer sein');
        }
        $this->passwort = $passwort;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getRegistriert_seit()
    {
        return new DateTime($this->registriert_seit);
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getRegistriert_seitFormatiert()
    {
        return $this->getRegistriert_seit()->format('d.m.Y');
    }

    /**
     * Setter
     *
     * @param string $registriert_seit
     * @return Benutzer
     */
    public function setRegistriert_seit($registriert_seit)
    {
        $this->registriert_seit = $registriert_seit;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getAnrede()
    {
        return $this->anrede;
    }

    /**
     * Setter
     *
     * @param string $anrede
     * @return Benutzer
     */
    public function setAnrede($anrede)
    {
        $this->anrede = $anrede;
        return $this;
    }

    public function validatePasswort($passwort)
    {
        return $this->passwort === $passwort;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSeminartetmine()
    {
        return Seminartermin::findByBenutzer($this);
    }

    /**
     * Fügt dem Benutzer einen neue Teilahme an einem Seminartermin hinzu.
     *
     * @param Seminartermin $seminartetmin
     * @return Benutzer
     */
    public function addSeminartermin(Seminartermin $seminartetmin)
    {
        // wir können die Zwischentabelle nicht befüllen, so lange der Seminartermin
        // nicht gespeichert ist.
        if ( ! $seminartetmin->getId() > 0 ) {
            $seminartetmin->save();
        }

        $sql = 'INSERT INTO nimmt_teil (benutzer_id, seminartermin_id) VALUES (?, ?)';
        $statement = Database::getInstance()->prepare($sql);
        $statement->execute(array( $this->getId(), $seminartetmin->getId() ));

        return $this;
    }

    /**
     * Gibt eine kurze Beschreibung des Objekts zurück
     *
     * @return string
     */
    public function __toString()
    {
        return htmlspecialchars(sprintf(
            '"%s %s %s" <%s>',
            $this->getAnrede(),
            $this->getVorname(),
            $this->getName(),
            $this->getEmail()
        ));
    }

    /**
     * Überladung des Hooks
     */
    public function _preInsert()
    {
        // vor dem ersten Einfügen wird das Registierungsdatum auf 'heute' gesetzt
        $this->setRegistriert_seit(strftime('%Y-%m-%d'));
    }

    public static function findByIds(array $ids)
    {
        $value = '(' . implode(', ', $ids) . ')';
        return static::findBy('id', $value, ' IN ');
    }

    /**
     * Findet Benutzer eines Seminartermins über die Zwischentabelle nimmt_teil
     *
     * Hier ist ein SQL JOIN notwendig, wobei die Spalten der Zwischentabelle nicht
     * im Objekt auftauchen sollen, also kein SELECT *.
     *
     * @param Seminartermin $seminartermin
     * @return array
     */
    public static function findBySeminartermin(Seminartermin $seminartermin)
    {
        $sql = sprintf(
            'SELECT %s FROM benutzer be JOIN nimmt_teil nt ON be.id = nt.benutzer_id WHERE nt.seminartermin_id = ?',
            implode(', ', static::getTableColumns())
        );
        $statement = Database::getInstance()->prepare($sql);
        $statement->execute(array( $seminartermin->getId() ));
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $statement->fetchAll();
    }

    public static function countBySeminartermin(Seminartermin $seminartermin)
    {
        $sql = 'SELECT COUNT(be.id) as count FROM benutzer be JOIN nimmt_teil nt ON be.id = nt.benutzer_id WHERE nt.seminartermin_id = ?';
        $statement = Database::getInstance()->prepare($sql);
        $statement->execute(array( $seminartermin->getId() ));
        $data = $statement->fetch();
        return $data['count'];
    }

    /**
     * Findet Benutzer, die nicht an einem Seminartermin teilnehmen über die Zwischentabelle nimmt_teil
     *
     * Hier ist ein SQL JOIN notwendig, wobei die Spalten der Zwischentabelle nicht
     * im Objekt auftauchen sollen, also kein SELECT *.
     *
     * @param Seminartermin $seminartermin
     * @return array
     */
    public static function excludeBySeminartermin(Seminartermin $seminartermin)
    {
        $teilnehmer = $seminartermin->getTeilnehmer();

        if ($teilnehmer) {
            // wenn es schon Teilnehmer gibt, finde alle Nicht-Teilnehmer
            $teilnehmer_ids = array();
            foreach ($teilnehmer as $t) {
                $teilnehmer_ids[] = $t->getId();
            }
            $sql = 'SELECT * FROM benutzer WHERE id NOT IN (' . implode(", ", $teilnehmer_ids) . ')';
        } else {
            // ansonsten finde alle Benutzer
            $sql = 'SELECT * FROM benutzer';
        }
        $statement = Database::getInstance()->prepare($sql);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $statement->fetchAll();
    }
}