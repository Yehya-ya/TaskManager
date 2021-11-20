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
            $table->foreignIdFor(Project::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Category::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class, 'assigned_user')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('project_id');
            $table->index(['project_id', 'category_id']);
            $table->index(['project_id', 'assigned_user']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
