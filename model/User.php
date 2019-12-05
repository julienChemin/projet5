<?php

namespace Chemin\ArtSchool\Model;

class User
{
	private $id,
			$name,
			$mail,
			$school,
			$password,
			$temporaryPassword,
			$beingReset,
			$nbWarning,
			$isBan,
			$dateBan,
			$isAdmin,
			$isModerator;

	public function __construct(array $data = null)
	{
		if (!empty($data)) {
			$this->hydrate($data);
		}
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
		return $this->isBan;
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

	//SETTERS
	public function setId(int $id)
	{
		if ($id > 0){
			$this->id = $id;
			return $this;
		}
	}

	public function setName(string $name)
	{
		if (strlen($name) > 0){
			$this->name = $name;
			return $this;
		}
	}

	public function setMail(string $mail)
	{
		if (strlen($mail) > 0){
			$this->mail = $mail;
			return $this;
		}
	}

	public function setSchool(string $school)
	{
		if (strlen($school) > 0){
			$this->school = $school;
			return $this;
		}
	}

	public function setPassword(string $password)
	{
		if (strlen($password) > 0){
			$this->password = $password;
			return $this;
		}
	}

	public function setTemporaryPassword(string $temporaryPassword)
	{
		if (strlen($temporaryPassword) > 0){
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
}
