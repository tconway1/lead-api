<?php

namespace App\Models\Traits;

trait SetNull
{
    public function setFieldsToNull(): void
    {
        foreach (array_keys($this->getAttributes()) as $modelAttr) {
            if (!in_array($modelAttr, $this->notNullable)) {
                $this->$modelAttr = null;
            }
        }
        $this->save();
    }
}
