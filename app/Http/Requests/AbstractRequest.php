<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use App\Exceptions\ValidationException;

abstract class AbstractRequest extends FormRequest
{
    /**
     * @var string $action
     */
    protected $action;

    /**
     * @var array $validationData
     */
    protected $validationData = [];

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->action = Route::getCurrentRoute()->getActionMethod();
    }

    /**
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (key_exists($this->action, $this->validationData)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array|mixed
     */
    public function rules()
    {
        if (key_exists($this->action, $this->validationData)) {
            return $this->validationData[$this->action];
        } else {
            return [];
        }
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setValidationData(array $data)
    {
        $this->validationData = $data;
        return $this;
    }
}