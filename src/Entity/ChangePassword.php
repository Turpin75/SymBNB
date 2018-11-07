<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;


class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(message="Le mot de passe saisi ne correspond pas au mode de passe actuel.")
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\Length(max=4096, maxMessage="Votre mot de passe ne peut dépasser {{limit}} caractères !")
     */
    private $oldPassword;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner ce champ.")
     * @Assert\Length(max=4096, maxMessage="Votre mot de passe ne peut dépasser {{limit}} caractères !")
     */
    private $newPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
