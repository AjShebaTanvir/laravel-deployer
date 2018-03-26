<?php

namespace Lorisleiva\LaravelDeployer\Test\Recipes;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class RollbackTest extends DeploymentTestCase
{
    protected $recipe = 'basic';

    /** @test */
    function a_rollback_with_no_previous_release_should_do_nothing_but_warn_user()
    {
        $output = $this->artisan('deploy:rollback');

        $this->assertContains('Executing task rollback', $output);
        $this->assertContains('No more releases you can revert to', $output);
    }

    /** @test */
    function a_rollback_should_symlink_to_the_previous_release()
    {
        /* 1st release */
        
        $this->runInRepository('touch unicorn.txt');
        $this->commitChanges();

        $this->artisan('deploy');

        $this->assertSuccessfulDeployment();
        $this->assertServerHas('unicorn.txt');

        /* 2nd release */

        $this->runInRepository('rm unicorn.txt');
        $this->commitChanges();

        $this->artisan('deploy');

        $this->assertSuccessfulDeployment();
        $this->assertServerMiss('unicorn.txt');

        /* Rollback */

        $this->artisan('deploy:rollback');

        $this->assertSuccessfulDeployment();
        $this->assertServerHas('unicorn.txt');
    }
}