<?php

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->foreignIdFor(Project::class)->constrained();
            $table->foreignIdFor(Category::class)->constrained();
            $table->foreignIdFor(User::class, 'assigned_user')->constrained('users')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
