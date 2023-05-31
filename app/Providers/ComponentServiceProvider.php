<?php

namespace App\Providers;

use Form;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ComponentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this
            ->registerLivewireComponents()
            ->registerFormComponents();
    }

    /**
     * Register livewire components
     */
    public function registerLivewireComponents()
    {
        if (! class_exists(Livewire::class)) {
            return $this;
        }

        Livewire::component('x-select-field', \App\Http\Livewire\Component\XSelectField::class);
        Livewire::component('x-media-attachment', \App\Http\Livewire\Component\MediaAttachment::class);
        Livewire::component('x-action-button', \App\Http\Livewire\Component\ActionButton::class);
        Livewire::component('x-activity-log', \App\Http\Livewire\Component\ActivityLog::class);
        Livewire::component('x-comments', \App\Http\Livewire\Component\Comments::class);
        Livewire::component('x-forms-htmleditor', \App\Http\Livewire\Component\HtmlEditor::class);

        return $this;
    }

    /**
     * Register form components
     */
    public function registerFormComponents()
    {
        Form::component('dropzone', 'components.form.dropzone', ['model', 'collection', 'attributes' => []]);
        Form::component('view', 'components.form.view', ['label', 'value' => null, 'icon' => 'dot-circle']);
        Form::component('filelist', 'components.form.filelist', ['directory', 'label' => 'Files']);
        Form::component('alert', 'components.form.alert', ['message', 'class' => 'info']);
        Form::component('datepicker', 'components.form.datepicker', ['name', 'value' => null, 'attributes' => []]);

        return $this;
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
