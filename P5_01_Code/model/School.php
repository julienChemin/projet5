<?php

namespace Chemin\ArtSchools\Model;

class School
{
    private $id,
        $idAdmin,
        $name,
        $nameAdmin,
        $mail, 
        $schoolGroups,
        $listSchoolGroups,
        $code,
        $nbEleve,
        $nbActiveAccount,
        $dateInscription,
        $logo,
        $isActive,
        $profileBannerInfo = null,
        $profileBanner,
        $noBanner,
        $profilePictureInfo = null,
        $profilePicture,
        $profilePictureOrientation,
        $profilePictureSize,
        $profileTextInfo = null,
        $profileTextBlock,
        $profileTextSchool;

    public function __construct(array $data = null)
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
        $this->init();
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value){
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function init()
    {
        $this->setListSchoolGroups($this->getSchoolGroups());
        $this->setProfileBannerInfo($this->getProfileBannerInfo());
        $this->setProfilePictureInfo($this->getProfilePictureInfo());
        $this->setProfileTextInfo($this->getProfileTextInfo());
        if ($this->getIsActive() || $this->getIsActive() === '1') {
            $this->setIsActive(true);
        } else {
            $this->setIsActive(false);
        }
    }

    //GETTERS
    public function getId()
    {
        return intval($this->id);
    }

    public function getIdAdmin()
    {
        return intval($this->idAdmin);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNameAdmin()
    {
        return $this->nameAdmin;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getSchoolGroups()
    {
        return $this->schoolGroups;
    }

    public function getListSchoolGroups()
    {
        return $this->listSchoolGroups;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getNbEleve()
    {
        return $this->nbEleve;
    }

    public function getNbActiveAccount()
    {
        return intval($this->nbActiveAccount);
    }

    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function getProfileBannerInfo()
    {
        return $this->profileBannerInfo;
    }

    public function getProfileBanner()
    {
        return $this->profileBanner;
    }

    public function getNoBanner()
    {
        return $this->noBanner;
    }

    public function getProfilePictureInfo()
    {
        return $this->profilePictureInfo;
    }

    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    public function getProfilePictureOrientation()
    {
        return $this->profilePictureOrientation;
    }

    public function getProfilePictureSize()
    {
        return $this->profilePictureSize;
    }

    public function getProfileTextInfo()
    {
        return $this->profileTextInfo;
    }

    public function getProfileTextBlock()
    {
        return $this->profileTextBlock;
    }

    public function getProfileTextSchool()
    {
        return $this->profileTextSchool;
    }

    //SETTERS
    public function setId(int $idSchool)
    {
        if ($idSchool > 0) {
            $this->id = $idSchool;
            return $this;
        }
    }

    public function setIdAdmin(int $idAdmin)
    {
        if ($idAdmin > 0) {
            $this->idAdmin = $idAdmin;
            return $this;
        }
    }

    public function setName(string $name)
    {
        if (strlen($name) > 0) {
            $this->name = $name;
            return $this;
        }
    }

    public function setNameAdmin(string $nameAdmin)
    {
        if (strlen($nameAdmin) > 0) {
            $this->nameAdmin = $nameAdmin;
            return $this;
        }
    }

    public function setMail(string $mail)
    {
        if (strlen($mail) > 0) {
            $this->mail = $mail;
            return $this;
        }
    }

    public function setSchoolGroups(string $schoolGroups)
    {
        if (strlen($schoolGroups) > 0) {
            $this->schoolGroups = $schoolGroups;
            return $this;
        }
    }

    public function setListSchoolGroups($list)
    {
        if ($list !== null) {
            $listSchoolGroups = explode(',', $list);
            $this->listSchoolGroups = array_slice($listSchoolGroups, 1);
            return $this;
        }
    }

    public function setCode(string $code)
    {
        if (strlen($code) > 0) {
            $this->code = $code;
            return $this;
        }
    }

    public function setNbEleve(int $nbEleve)
    {
        if ($nbEleve > 0) {
            $this->nbEleve = $nbEleve;
            return $this;
        }
    }

    public function setNbActiveAccount(int $nbActiveAccount)
    {
        if ($nbActiveAccount >= 0) {
            $this->nbActiveAccount = $nbActiveAccount;
            return $this;
        }
    }

    public function setDateInscription($date)
    {
        if (!empty($date)) {
            $this->dateInscription = $date;
            return $this;
        }
    }

    public function setLogo(string $logoPath)
    {
        if (strlen($logoPath) > 0) {
            $this->logo = $logoPath;
            return $this;
        }
    }

    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function setProfileBannerInfo($profileBannerInfo)
    {
        if ($profileBannerInfo === null) {
            $this->setProfileBanner('noBanner');
            $this->setNoBanner(true);
        } elseif (is_string($profileBannerInfo) && strlen($profileBannerInfo) > 0) {
            $infos = explode(' ', $profileBannerInfo);
            if (strpos($infos[0], 'http://') === 0) {
                $infos[0] = str_replace('http://localhost/P5_Chemin_Julien/P5_01_Code/', '', $infos[0]);
            }
            $this->setProfileBanner($infos[0]);
            if ($infos[1] === 'true') {
                $this->setNoBanner(true);
            } else {
                $this->setNoBanner(false);
            }
        }
        return $this;
    }

    public function setProfileBanner(string $profileBanner)
    {
        if (strlen($profileBanner) > 0) {
            $this->profileBanner = $profileBanner;
        }
        return $this;
    }

    public function setNoBanner(bool $noBanner)
    {
        $this->noBanner = $noBanner;
        return $this;
    }

    public function setProfilePictureInfo($profilePictureInfo)
    {
        if ($profilePictureInfo === null) {
            $this->setProfilePicture('public/images/question-mark.png');
            $this->setProfilePictureOrientation('widePicture');
            $this->setProfilePictureSize('smallPicture');
        } elseif (is_string($profilePictureInfo) && strlen($profilePictureInfo) > 0) {
            $infos = explode(' ', $profilePictureInfo);
            if (strpos($infos[0], 'http://') === 0) {
                $infos[0] = str_replace('http://localhost/P5_Chemin_Julien/P5_01_Code/', '', $infos[0]);
            }
            $this->setProfilePicture($infos[0]);
            $this->setProfilePictureOrientation($infos[1]);
            $this->setProfilePictureSize($infos[2]);
        }
        return $this;
    }

    public function setProfilePicture(string $profilePicture)
    {
        if (strlen($profilePicture) > 0) {
            $this->profilePicture = $profilePicture;
        }
        return $this;
    }

    public function setProfilePictureOrientation(string $profilePictureOrientation)
    {
        if (strlen($profilePictureOrientation) > 0) {
            $this->profilePictureOrientation = $profilePictureOrientation;
        }
        return $this;
    }

    public function setProfilePictureSize(string $profilePictureSize)
    {
        if (strlen($profilePictureSize) > 0) {
            $this->profilePictureSize = $profilePictureSize;
        }
        return $this;
    }

    public function setProfileTextInfo($profileTextInfo)
    {
        if ($profileTextInfo === null) {
            $this->setProfileTextBlock('elemCenter');
            $this->setProfileTextSchool('elemStart');
        } else {
            $infos = explode(' ', $profileTextInfo);
            $this->setProfileTextBlock($infos[0]);
            $this->setProfileTextSchool($infos[1]);
        }
        return $this;
    }

    public function setProfileTextBlock(string $profileTextBlock)
    {
        if (strlen($profileTextBlock) > 0) {
            $this->profileTextBlock = $profileTextBlock;
        }
        return $this;
    }

    public function setProfileTextSchool(string $profileTextSchool)
    {
        if (strlen($profileTextSchool) > 0) {
            $this->profileTextSchool = $profileTextSchool;
        }
        return $this;
    }
}
