<?php

namespace App\Services;
use App\Entity\User;
use App\Repository\UserRepository;

class PointsService {

    protected UserRepository $userRepository;
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function addPoints(int $amount, User $user) {
        $user->setPoints($user->getPoints() + $amount);
        $user = $this->_checkLevel($user);
        $this->userRepository->save($user, true);
    }

    private function _checkLevel(User $user) : User {
        $currentLevel = $user->getLevels();
        $nextLevel = 1000 + $currentLevel * 1000;

        if($user->getPoints() >= $nextLevel) {
            $user->setLevels($currentLevel + 1);
            return $this->_checkLevel($user);
        } 

        return $user;
    }
}