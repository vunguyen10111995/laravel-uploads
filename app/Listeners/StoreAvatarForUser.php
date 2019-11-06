<?php

namespace App\Listeners;

class StoreAvatarForUser extends Listener
{
    public function preprocess()
    {
        $upload = $this->event->user->avatarImage;

        if ($upload) {
            $this->getHandler()->delete($upload);
            $upload->delete();
        }
    }

    public function postprocess()
    {
        $this->event->user->setAttribute('avatar', $this->event->upload->hashed_name)->save();
    }
}
