<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('properties', function (Blueprint $table) {
      $table->id();
      $table->string('address')->nullable();
      $table->string('city')->nullable();
      $table->string('state')->nullable();
      $table->string('zip')->nullable();
      $table->string('county')->nullable();
      $table->json('geo')->nullable();
      $table->string('thumbnail_url')->nullable();
      $table->string('parcel_id')->nullable(); // property id - unique identifier for the property 
      $table->string('price')->nullable();
      $table->string('area')->nullable();
      $table->string('source')->nullable(); // where the property was scraped from
      $table->text('url')->nullable(); // url of the property
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('properties');
  }
};
