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
      $table->string('parcel_id')->nullable(); // property id - unique identifier for the property
      $table->string('owner_name')->nullable();
      $table->string('owner_email')->nullable();
      $table->string('price')->nullable();
      $table->string('bedrooms')->nullable();
      $table->string('bathrooms')->nullable();
      $table->string('area')->nullable();
      $table->string('source')->nullable(); // where the property was scraped from
      $table->text('url')->nullable(); // url of the property
      $table->string('property_type')->nullable();
      $table->string('status')->nullable();
      $table->date('date_listed')->nullable();
      $table->string('thumbnail_url')->nullable();
      $table->dateTime('scraped_date')->nullable();
      $table->text('notes')->nullable();
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
