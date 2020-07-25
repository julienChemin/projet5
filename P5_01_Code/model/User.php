<?php

namespace Chemin\ArtSchool\Model;

class User
{
    private $id,
    $name,
    $mail,
    $school,
    $schoolGroup,
    $password,
    $temporaryPassword,
    $beingReset,
    $nbWarning,
    $isBan,
    $dateBan,
    $isAdmin,
    $isModerator,
    $dateDeadline,
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
    $profileTextPseudo,
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
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function init()
    {
        $this->setProfileBannerInfo($this->getProfileBannerInfo());
        $this->setProfilePictureInfo($this->getProfilePictureInfo());
        $this->setProfileTextInfo($this->getProfileTextInfo());
        if ($this->getIsActive() === '1') {
            $this->setIsActive(true);
        } else {
            $this->setIsActive(false);
        }
    }

    //GETTERS
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getSchool()
    {
        return $this->school;
    }

    public function getSchoolGroup()
    {
        return $this->schoolGroup;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getTemporaryPassword()
    {
        return $this->temporaryPassword;
    }

    public function getBeingReset()
    {
        return $this->beingReset;
    }

    public function getNbWarning()
    {
        return $this->nbWarning;
    }

    public function getIsBan()
    {
        return boolval($this->isBan);
    }

    public function getDateBan()
    {
        return $this->dateBan;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function getIsModerator()
    {
        return $this->isModerator;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function getDateDeadline()
    {
        return $this->dateDeadline;
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

    public function getProfileTextPseudo()
    {
        return $this->profileTextPseudo;
    }

    public function getProfileTextSchool()
    {
        return $this->profileTextSchool;
    }

    //SETTERS
    public function setId(int $id)
    {
        if ($id > 0) {
            $this->id = $id;
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

    public function setMail(string $mail)
    {
        if (strlen($mail) > 0) {
            $this->mail = $mail;
            return $this;
        }
    }

    public function setSchool(string $school)
    {
        if (strlen($school) > 0) {
            $this->school = $school;
            return $this;
        }
    }

    public function setSchoolGroup(string $schoolGroup)
    {
        if (strlen($schoolGroup) > 0) {
            $this->schoolGroup = $schoolGroup;
            return $this;
        }
    }

    public function setPassword(string $password)
    {
        if (strlen($password) > 0) {
            $this->password = $password;
            return $this;
        }
    }

    public function setTemporaryPassword(string $temporaryPassword)
    {
        if (strlen($temporaryPassword) > 0) {
            $this->temporaryPassword = $temporaryPassword;
            return $this;
        }
    }

    public function setBeingReset(bool $beingReset)
    {
        $this->beingReset = $beingReset;
        return $this;
    }

    public function setNbWarning(int $nbWarning)
    {
        $this->nbWarning = $nbWarning;
        return $this;
    }

    public function setIsBan(bool $isBan)
    {
        $this->isBan = $isBan;
        return $this;
    }

    public function setDateBan($dateBan)
    {
        if (!empty($dateBan)) {
            $this->dateBan = $dateBan;
        }
    }

    public function setIsAdmin(bool $isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setIsModerator(bool $isModerator)
    {
        $this->isModerator = $isModerator;
        return $this;
    }

    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function setDateDeadline($date)
    {
        if (!empty($date)) {
            $this->dateDeadline = $date;
            return $this;
        }
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
            $this->setProfileTextPseudo('elemStart');
            $this->setProfileTextSchool('elemStart');
        } else {
            $infos = explode(' ', $profileTextInfo);
            $this->setProfileTextBlock($infos[0]);
            $this->setProfileTextPseudo($infos[1]);
            $this->setProfileTextSchool($infos[2]);
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

    public function setProfileTextPseudo(string $profileTextPseudo)
    {
        if (strlen($profileTextPseudo) > 0) {
            $this->profileTextPseudo = $profileTextPseudo;
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
