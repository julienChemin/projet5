<?php
namespace Chemin\ArtSchools\Model;
use Chemin\ArtSchools\Model\CvSectionManager;

class CvManager extends CvSectionManager
{
    public static $CV_OBJECT_TYPE = 'Chemin\ArtSchools\Model\Cv';
    public static $CV_TABLE_NAME = 'as_cv';
    public static $CV_TABLE_CHAMPS = 'id, idUser, isOnline, displayNavbar, shortLink';

    public function getCv(int $idUser = 0)
    {
        if ($idUser > 0) {
            $result = ['info' => [], 'sections' => [], 'blocks' => []];

            $result['info'] = $this->getCvInfo($idUser);
            $cvContent = $this->getSections($idUser);
            $result['sections'] = $cvContent['sections'];
            $result['blocks'] = $cvContent['blocks'];

            return $result;
        } else {
            return false;
        }
    }

    public function updateCv(int $idUser, string $elem = null, $value, bool $isBool = false)
    {
        if ($elem && str_contains(static::$CV_TABLE_CHAMPS, $elem)) {
            if ($isBool) {
                $this->sql(
                    'UPDATE ' . static::$CV_TABLE_NAME . ' 
                    SET ' . $elem . ' = :value 
                    WHERE idUser = :idUser', 
                    [':value' => intval($value), ':idUser' => $idUser]
                );
            } else {
                $this->sql(
                    'UPDATE ' . static::$CV_TABLE_NAME . ' 
                    SET ' . $elem . ' = :value 
                    WHERE idUser = :idUser', 
                    [':value' => $value, ':idUser' => $idUser]
                );
            }
    
            return true;
        } else {
            return false;
        }
    }

    public function deleteCv(int $idUser = null)
    {
        if (!empty($idUser)) {
            $sections = $this->getSections($idUser, false);

            if (!empty($sections) && count($sections) > 0) {
                foreach ($sections as $section) {
                    $this->deleteSection($section, true);
                }
            }
        }

        $this->deleteCvInfo($idUser);
    }

    public function setupDefaultCv(User $user)
    {
        $this->setCvInfo($user->getId());

        // header
        $idNewHeader = $this->setSection($user->getId(), 'en-tête', true, '90vh', 'center', 'between', $user->getProfileBanner());

        $headerContent = '<h1>' . $user->getLastName() . ' ' . $user->getFirstName() . '</h1>
            <p>Personnalisez votre CV en ouvrant <span style="color:#CF8B3F">le menu <i class="fas fa-pencil-alt"></i> à droite</span></p>
            <p>N\'oubliez pas de définir une adresse dans vos <a href="index.php?action=settings">paramètres</a>, sinon votre CV ne sera pas visible.</p>';
        $this->setBlock($idNewHeader, $user->getId(), $headerContent, 'medium', '22, 22, 23', 0.8, null, null, 5);

        // section 1
        $idSectionAbout = $this->setSection($user->getId(), 'à propos', true, null, 'center', 'around');

        $sectionAboutContentOne = '<h1 style="text-align:center;">À propos</h1>';
        $this->setBlock($idSectionAbout, $user->getId(), $sectionAboutContentOne, 'large');

        $sectionAboutContentTwo = '<h2>C\'est le moment de raconter ta vie !</h2><p>Fait un cours résumé de ta personne :</p>
            <ul style="margin-left:10px;"><li><p>- Qui es-tu ?</p></li><li><p>- Que fais-tu dans la vie ?</p></li>
            <li><p>- Qu\'est-ce qui te caractérise ?</p></li><li><p>- Loisirs / passions / rêves / etc</p></li></ul>';
        $this->setBlock($idSectionAbout, $user->getId(), $sectionAboutContentTwo, 'medium');

        $sectionAboutContentThree = '<h2>Mets tes qualités en avant :</h2><p>Tu as des diplômes ?</p><p>Tu as fait des formations ?</p>
            <p>Tu maitrises des connaissances spécifiques à ton domaine ?</p>
            <p>Tu peux contrôler les éléments ou voir à travers les murs ? C\'est le moment d\'en parler !</p>';
        $this->setBlock($idSectionAbout, $user->getId(), $sectionAboutContentThree, 'small', '22, 22, 23', 1, 1, '38, 38, 39', 5);

        $sectionAboutContentFour = '<div style="text-align:center;"></div><p>Tu peux décrire ton parcours scolaire</p><p>Si tu as déjà eu des expériences professionnelles parles-en !</p>
            <p>N\'hésite pas à imager tout ça, mais ne soit pas dans l\'excès, un CV doit être claire, lisible, et les informations importantes doivent être mise en avant</p></div>';
        $this->setBlock($idSectionAbout, $user->getId(), $sectionAboutContentFour, 'large');

        // section 2
        $idSectionImg = $this->setSection($user->getId(), 'section', false, '60vh', 'center', 'between', 'public/images/default_cv_banner_1.jpg', true);

        $sectionImgContentOne = '<h2>Ajoute une section un peu "fancy"</h2>
            <p>C\'est très à la mode de mettre une image d\'ampoule allumée, ça fait ressortir ton côté créatif.</p>';
        $this->setBlock($idSectionImg, $user->getId(), $sectionImgContentOne, 'small');

        $sectionImgContentTwo = '<h2>Un petit conseil :</h2>
            <p>Le Cv doit te représenter ! Mais si tu es du genre à ne pas ranger ta chambre... 
            essaye de représenter une autre facette de ta personnalité</p>';
        $this->setBlock($idSectionImg, $user->getId(), $sectionImgContentTwo, 'small');

        // footer
        $idNewFooter = $this->setSection($user->getId(), 'contact', true, '40vh', 'center', 'around');

        $footerContentOne = '<h2 style="text-align:center;">Contact</h2>';
        $this->setBlock($idNewFooter, $user->getId(), $footerContentOne, 'large');
        
        $footerContentTwo = '<p>Laisse ton empreinte digitale !</p>';
        $this->setBlock($idNewFooter, $user->getId(), $footerContentTwo, 'small');

        $footerContentThree = '<p>Donne des liens vers tes réseaux sociaux, et autres liens pertinents</p>';
        $this->setBlock($idNewFooter, $user->getId(), $footerContentThree, 'small');

        $footerContentFour = '<p>Et bien sûr, un moyen de te contacter ! Téléphone / Mail / Etc.</p>';
        $this->setBlock($idNewFooter, $user->getId(), $footerContentFour, 'small');
    }

    public function userHaveCv(int $idUser = 0) {
        if (!empty($idUser) && !empty($this->getCvInfo($idUser))) {
            return true;
        } else {
            return false;
        }
    }

    /*-------------------------------------------------------------------------------------
    ----------------------------------- PRIVATE FUNCTION ------------------------------------
    -------------------------------------------------------------------------------------*/

    private function setCvInfo(int $idUser)
    {
        $this->sql(
            'INSERT INTO ' . static::$CV_TABLE_NAME . ' (idUser) 
            VALUES (:idUser)', 
            [':idUser' => $idUser]
        );

        return $this->getLastInsertId();
    }

    private function getCvInfo(int $idUser)
    {
        $q = $this->sql(
            'SELECT ' . static::$CV_TABLE_CHAMPS . ' 
            FROM ' . static::$CV_TABLE_NAME . ' 
            WHERE idUser = :idUser', 
            [':idUser' => $idUser]
        );

        $result = $q->fetchObject(static::$CV_OBJECT_TYPE);
        $q->closeCursor();

        return $result;
    }

    private function deleteCvInfo(int $idUser)
    {
        $this->sql(
            'DELETE FROM ' . static::$CV_TABLE_NAME . ' 
            WHERE idUser = :idUser', 
            [':idUser' => $idUser]
        );
    }
}
