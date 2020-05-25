<?php

namespace Chemin\ArtSchool\Model;

class Comment
{
	private $id,
			$idPost,
			$idAuthor,
			$nameAuthor,
			$profilePictureAuthor,
			$content,
			$datePublication,
			$nbReport;

	public function __construct(array $data = null)
	{
		if (!empty($data)) {
			$this->hydrate($data);
		}
	}

	public function hydrate(array $data)
	{
		foreach ($data as $key => $value){
			$method = 'set' . ucfirst($key);

			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}

	//GETTERS
	public function getId()
	{
		return $this->id;
	}

	public function getIdPost()
	{
		return $this->idPost;
	}

	public function getIdAuthor()
	{
		return $this->idAuthor;
	}

	public function getNameAuthor()
	{
		return $this->nameAuthor;
	}

	public function getProfilePictureAuthor()
	{
		return $this->profilePictureAuthor;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getDatePublication()
	{
		return $this->datePublication;
	}

	public function getNbReport()
	{
		return $this->nbReport;
	}

	//SETTERS
	public function setId(int $idComment)
	{
		if ($idComment > 0) {
			$this->id = $idComment;
			return $this;
		}
	}

	public function setIdPost(int $idPost)
	{
		if ($idPost > 0) {
			$this->idPost = $idPost;
			return $this;
		}
	}

	public function setIdAuthor(int $idAuthor)
	{
		if ($idAuthor > 0) {
			$this->idAuthor = $idAuthor;
			return $this;
		}
	}

	public function setNameAuthor(string $nameAuthor)
	{
		if (strlen($nameAuthor) > 0) {
			$this->nameAuthor = $nameAuthor;
			return $this;
		}
	}

	public function setProfilePictureAuthor(string $profilePictureAuthor)
	{
		if (strlen($profilePictureAuthor) > 0) {
			$this->profilePictureAuthor = $profilePictureAuthor;
			return $this;
		}
	}

	public function setContent(string $content)
	{
		if (strlen($content) > 0) {
			$this->content = $content;
			return $this;
		}
	}

	public function setDatePublication($date)
	{
		if (!empty($date)) {
			$this->datePublication = $date;
			return $this;
		}
	}

	public function setNbReport(int $nbReport)
	{
		if ($nbReport >= 0) {
			$this->nbReport = $nbReport;
			return $this;
		}
	}
}
