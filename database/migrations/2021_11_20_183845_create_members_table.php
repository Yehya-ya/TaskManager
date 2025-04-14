<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Project::class)->constrained()->onDelete('cascade');
            $table->string('email');
            $table->timestamps();

            $table->index(['user_id', 'project_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
};
