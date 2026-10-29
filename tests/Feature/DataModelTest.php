<?php

use App\Enums\ApplicationStatus;
use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Application;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\QueryException;

it('seeds every role with the expected permissions', function () {
    $hr = User::factory()->hr()->create();
    $direction = User::factory()->direction()->create();
    $manager = User::factory()->sectorManager()->create();
    $member = User::factory()->member()->create();

    expect($hr->can(Permission::RecruitmentValidate->value))->toBeTrue()
        ->and($hr->can(Permission::UsersManage->value))->toBeTrue()
        ->and($direction->can(Permission::RecruitmentValidate->value))->toBeFalse()
        ->and($direction->can(Permission::ApplicationsDecide->value))->toBeTrue()
        ->and($manager->can(Permission::OpinionsFirst->value))->toBeTrue()
        ->and($manager->can(Permission::ApplicationsViewAll->value))->toBeFalse()
        ->and($member->getAllPermissions())->toBeEmpty()
        ->and($member->hasRole(Role::Member->value))->toBeTrue();
});

it('generates a unique slug for job posts', function () {
    $a = JobPost::factory()->create(['title' => 'Chargé de mission']);
    $b = JobPost::factory()->create(['title' => 'Chargé de mission']);

    expect($a->slug)->toBe('charge-de-mission')
        ->and($b->slug)->toBe('charge-de-mission-2');
});

it('allows only one active application per user and job post', function () {
    $application = Application::factory()->create();

    expect(fn () => Application::factory()->create([
        'user_id' => $application->user_id,
        'job_post_id' => $application->job_post_id,
    ]))->toThrow(QueryException::class);
});

it('allows a new application once the previous one is inactive', function () {
    $application = Application::factory()->status(ApplicationStatus::Withdrawn)->create();

    $again = Application::factory()->create([
        'user_id' => $application->user_id,
        'job_post_id' => $application->job_post_id,
    ]);

    expect($again->exists)->toBeTrue();
});
