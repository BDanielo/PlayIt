<?php

namespace App\Services;

use App\Entity\Badge;
use App\Repository\BadgeRepository;
use App\Repository\UserRepository;
use App\Entity\User;

class BadgeService
{

    protected UserRepository $userRepository;
    protected BadgeRepository $badgeRepository;
    public function __construct(UserRepository $userRepository, BadgeRepository $badgeRepository)
    {
        $this->userRepository = $userRepository;
        $this->badgeRepository = $badgeRepository;
        define("LEVEL5", "LEVEL5");
        define("FIRSTGAME", "FIRSTGAME");
        define("FIVEGAMES", "FIVEGAMES");
        define("FIRSTPUBLISH", "FIRSTPUBLISH");
        define("FIRSTSOLD", "FIRSTSOLD");
        define("HUNDREDSOLD", "HUNDREDSOLD");
    }

    public function checkOnOrder(User $user)
    {

        if ($user->getLevels() > 5 && !$this->_hasBadge(LEVEL5, $user)) {
            // $user->addBadge($this->_createBadge(LEVEL5, $user));
            $this->_addBadgeToUser(LEVEL5, $user);
        }

        if ($user->getGamesOwned()->count() > 1 && !$this->_hasBadge(FIRSTGAME, $user)) {
            // $user->addBadge($this->_createBadge(FIRSTGAME, $user));
            $this->_addBadgeToUser(FIRSTGAME, $user);
        }

        if ($user->getGamesOwned()->count() >= 5 && !$this->_hasBadge(FIVEGAMES, $user)) {
            // $user->addBadge($this->_createBadge(FIVEGAMES, $user));
            $this->_addBadgeToUser(FIVEGAMES, $user);
        }

        $this->userRepository->save($user, true);
    }

    public function checkOnDevelopper(User $user)
    {

        if ($user->getGamesPublished() > 0 && !$this->_hasBadge(FIRSTGAME, $user)) {
            // $user->addBadge($this->_createBadge(FIRSTPUBLISH, $user));
            $this->_addBadgeToUser(FIRSTPUBLISH, $user);
        }

        foreach ($user->getGamesPublished() as $key => $game) {

            if ($game->getSold() >= 1 && !$this->_hasBadge(FIRSTSOLD, $user)) {
                // $user->addBadge($this->_createBadge(FIRSTSOLD, $user));
                $this->_addBadgeToUser(FIRSTSOLD, $user);
            }

            if ($game->getSold() >= 100 && !$this->_hasBadge(HUNDREDSOLD, $user)) {
                // $user->addBadge($this->_createBadge(FIRSTSOLD, $user));
                $this->_addBadgeToUser(HUNDREDSOLD, $user);
            }
        }

        $this->userRepository->save($user, true);
    }

    private function _hasBadge(string $name, User $user)
    {
        $badgesOwned = $user->getBadges();
        foreach ($badgesOwned as $key => $badge) {
            if ($badge->getName() == $name) {
                return true;
            }
        }
        return false;
    }

    private function _createBadge(string $name, User $user)
    {
        $badge = $this->badgeRepository->findOneBy(array('name' => $name));
        $badge->addUser($user);
        return $badge;
    }

    private function _addBadgeToUser(string $name, User $user)
    {
        $badge = $this->badgeRepository->findOneBy(array('name' => $name));
        if ($badge) {
            $badge->addUser($user);
            $this->badgeRepository->save($badge, true);
            $user->addBadge($badge);
            $this->userRepository->save($user, true);
            return true;
        }
        return false;
    }
}
