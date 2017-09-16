<?php

namespace Service;

use Entities\ViewRecord;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ViewRecordFactory
{
    const COOKIE_NAME = 'ua';

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return ViewRecord|false
     */
    public function fromRequest(Request $request)
    {
        $cookie = $request->cookies->get(self::COOKIE_NAME);

        if (!$cookie) {
            return false;
        }

        $data = $request->request->all();

        if (!$this->isValidData($data)) {
            return false;
        }

        $record = new ViewRecord($cookie, $data['type'], $data['payload']);

        return $record;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    private function isValidData($data)
    {
        $constraint = new Assert\Collection([
            'type' => new Assert\Choice([
                'choices' => ViewRecord::getTypes()
            ]),
            'payload' => new Assert\Regex([
                'pattern' => '/^(?:|25|50|75)$/'
            ]),
        ]);

        $errors = $this->validator->validate($data, $constraint);

        return count($errors) == 0;
    }
}
