<?php

namespace EdpDiscuss\Model\Message;

use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcBase\Mapper\Exception\InvalidArgumentException;
use EdpDiscuss\Model\Message\MessageInterface;
use ZfcUser\Entity\User;
use ZfcUser\Entity\UserInterface;
use ZfcUser\Mapper\UserHydrator;

class MessageHydrator extends ClassMethods
{
    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        if (!$object instanceof MessageInterface) {
            throw new InvalidArgumentException('$object must be an instance of EdpDiscuss\Model\Message\MessageInterface');
        }
        $data = parent::extract($object);
        
        $user = $object->getAuthorUser();
        if ($user instanceof UserInterface) {
            $data['author_user_id'] = $user->getId();    
        }
        unset($data['author_user']);
        
        $thread = $object->getThread();
        $data['thread_id'] = (int)$thread->getThreadId();
        unset($data['thread']);
        
        $data['post_time'] = $data['post_time']->format('Y-m-d H:i:s');
        
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return MessageInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof MessageInterface) {
            throw new InvalidArgumentException('$object must be an instance of EdpDiscuss\Model\Message\MessageInterface');
        }
        
        parent::hydrate($data, $object);
        
        if ($data['author_user_id']) {
            $user = new User;
            $hydrator = new UserHydrator;
            $data = $this->mapField('author_user_id', 'user_id', $data);
            $hydrator->hydrate($data, $user);
            $object->setAuthorUser($user);
        }
        
        return $object;
    }

    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }
}
