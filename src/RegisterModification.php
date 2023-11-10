<?php

namespace Approval;

use Approval\Models\Modification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RegisterModification
{
    private string $modelName;
    private string|null $modelId = null;
    private array $data = [];
    private Modification $modification;
    private bool $isUpdate = false;

    public static function make(): self
    {
        return new static;
    }

    public function setModel(Model $model): static
    {
        $this->modelName = $model::class;
        $this->modelId = $model->id;

        return $this;
    }

    public function isUpdate(bool $value = true): self
    {
        $this->isUpdate = $value;

        return $this;
    }

    private function getModelId(): string|null
    {
        return $this->modelId;
    }

    public function setModelId(string $modelId): self
    {
        $this->modelId = $modelId;

        return $this;
    }

    public function getModification(): Modification
    {
        return $this->modification;
    }

    public function setModelName(string $modelName): self
    {
        $this->modelName = $modelName;

        return $this;
    }

    private function getModelName(): string
    {
        return $this->modelName;
    }

    public function setData(array $data = []): self
    {
        $this->data = $data;

        return $this;
    }

    private function getData(): array
    {
        return $this->data;
    }

    private function getModifiedData(): array
    {
        $modifiedData = [];

        if(count($this->getData())){
            foreach ($this->getData() as $key => $value){
                $modifiedData[$key] = ['modified' => $value, 'original' => null];
            }
        }

        return $modifiedData;
    }

    public function save():self
    {
        $this->modification = Modification::create([
            'modifiable_type' => $this->getModelName(),
            'modifiable_id' => $this->getModelId(),
            'modifier_id' => Auth::id(),
            'modifier_type' => Auth::user()::class,
            'is_update' => $this->isUpdate,
            'md5' => md5(Carbon::now()->format('Y-m-d-H-i-s')),
            'modifications' => $this->getModifiedData()
        ]);

        return $this;
    }
}
