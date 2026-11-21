<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('short');
            $table->string('icon')->default('fa-helmet-safety');
            $table->text('teaser');
            $table->text('intro');
            $table->text('intro2')->nullable();
            $table->json('prestations')->nullable();
            $table->json('beneficiaires')->nullable();
            $table->json('methode')->nullable();
            $table->json('avantages')->nullable();
            $table->string('image')->nullable();
            $table->string('image2')->nullable();
            $table->string('hero_image')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('location');
            $table->string('category');
            $table->string('tags')->default('');
            $table->text('description');
            $table->string('programme')->nullable();
            $table->string('year')->nullable();
            $table->string('status')->nullable();
            $table->json('service_slugs')->nullable();
            $table->string('cover')->nullable();
            $table->string('ba_before')->nullable();
            $table->string('ba_after')->nullable();
            $table->string('ba_label_before')->nullable();
            $table->string('ba_label_after')->nullable();
            $table->string('video_url')->nullable();
            $table->text('testimonial')->nullable();
            $table->string('testimonial_author')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });

        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category');
            $table->text('excerpt');
            $table->longText('body');
            $table->string('image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('client_references', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sector');
            $table->string('prestation');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('logo')->nullable();
            $table->boolean('logo_authorized')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('contract');
            $table->string('location');
            $table->string('experience');
            $table->json('missions')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('prestation');
            $table->string('nature');
            $table->string('localisation');
            $table->unsignedInteger('surface')->nullable();
            $table->string('delai')->nullable();
            $table->string('budget')->nullable();
            $table->text('description');
            $table->string('nom');
            $table->string('entreprise')->nullable();
            $table->string('tel');
            $table->string('whatsapp')->nullable();
            $table->string('email');
            $table->string('rdv')->nullable();
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('quote_request_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('nom');
            $table->string('tel')->nullable();
            $table->string('email');
            $table->string('sujet');
            $table->text('message');
            $table->string('status')->default('new');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('nom');
            $table->string('poste');
            $table->string('tel');
            $table->string('email');
            $table->text('message')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('cv_name')->nullable();
            $table->string('status')->default('new');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('brochure_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email');
            $table->string('entreprise')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('site_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index();       // view | whatsapp | call | form
            $table->string('path')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 60)->nullable();  // provenance (Google, Facebook, direct…)
            $table->string('visitor', 64)->nullable(); // empreinte anonyme (hash)
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        foreach (['site_events', 'newsletter_subscribers', 'brochure_requests', 'job_applications', 'contact_messages',
                  'quote_request_files', 'quote_requests', 'job_offers', 'client_references', 'posts', 'project_images',
                  'projects', 'services', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
