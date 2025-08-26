<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->setupPlain();
    }
    public function setupPlain(){
        Schema::create('employment_apply_logs', function (Blueprint $table) {
            $table->uid();          // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->jsonb('user_data'); // تغيير الاسم إلى user_data
            $table->dates();        // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_amas', function (Blueprint $table) {
            $table->uid();              // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->string('text')->unique();  // تغيير الاسم إلى text مع الحفاظ على الـ unique
            $table->dates();            // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_annonces', function (Blueprint $table) {
            $table->uid();
            $table->uuid('annonce_id');
            $table->longText('text')->unique();
            $table->enum('status', ['publish', 'draft'])->default('draft');
            $table->dates();
        });
        Schema::create('employment_armies', function (Blueprint $table) {
            $table->uid();              // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->string('text')->unique();  // تغيير الاسم إلى text مع الحفاظ على الـ unique
            $table->dates();            // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_committees', function (Blueprint $table) {
            $table->uid();
            $table->uuid('annonce_id');
            $table->string('number')->nullable();
            $table->string('name')->nullable();
            $table->dateTime('Committee_date')->nullable();
            $table->enum('type', ['editorial', 'practical', 'interview']);
            $table->string('text')->nullable();
            $table->dates();
        });
        Schema::create('employment_dinamic_pages', function (Blueprint $table) {
            $table->uid();
            $table->string('text');
            $table->string('control');
            $table->string('function');
            $table->dates();
        });
        Schema::create('employment_drivers', function (Blueprint $table) {
            $table->uid();              // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->string('text')->unique();  // تغيير الاسم إلى text مع الحفاظ على الـ unique
            $table->dates();            // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_grievance', function (Blueprint $table) {
            $table->uid();
            $table->uuid('people_id');
            $table->uuid('stage_id');
            $table->dates();
        });
        Schema::create('employment_health', function (Blueprint $table) {
            $table->uid();                             // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->uuid('father')->nullable(); // استخدام foreignUuid مع تطبيق قاعدة snake_case
            $table->string('text');                    // تعديل اسم العمود إلى snake_case
            $table->dates();                           // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_included_files', function (Blueprint $table) {
            $table->uid();                            // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->text('file_name');                // تعديل اسم العمود إلى snake_case
            $table->enum('checked', ['mandatory', 'Non-binding', 'According_to_the_job'])->default('mandatory');  // enum مع القيم الافتراضية
            $table->dates();                          // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_instructions', function (Blueprint $table) {
            $table->uid();                      // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->longText('text');            // تعديل اسم العمود إلى snake_case
            $table->dates();                    // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_jobs', function (Blueprint $table) {
            $table->uid();
            $table->uuid('annonce_id')->index();
            $table->string('code');
            $table->uuid('job_name_id');
            $table->uuid('group_id');
            $table->uuid('job_title_id');
            $table->string('description')->nullable();
            $table->integer('count')->default(0);
            $table->date('age_in');
            $table->integer('age');
            $table->enum('driver', [1,0])->default(1);
            $table->enum('status', ['publish', 'draft'])->default('publish');
            $table->dates();
        });
        Schema::create('employment_committees_persons', function (Blueprint $table) {
            $table->uid();
            $table->enum('position', ['manager', 'employee'])->default('employee');
            $table->string('name');
            $table->string('mon');
            $table->longText('signs')->nullable();
            $table->dates();
        });
        Schema::create('employment_marital_status', function (Blueprint $table) {
            $table->uid();                      // دالة uid() لإنشاء UUID مع تعيينه كمفتاح أساسي
            $table->string('text');              // تعديل اسم العمود إلى snake_case
            $table->dates();                    // دالة dates() التي تحتوي على timestampsTz و softDeletes
        });
        Schema::create('employment_people', function (Blueprint $table) {
            $table->uid();
            $table->uuid('annonce_id')->index();
            $table->uuid('job_id')->index();
            $table->string('nid');
            $table->enum('sex', [0, 1])->default(1);
            $table->string('fname');
            $table->string('sname');
            $table->string('tname');
            $table->string('lname');
            $table->uuid('live_gov');
            $table->uuid('live_city');
            $table->mediumText('live_address');
            $table->uuid('born_gov');
            $table->uuid('born_city');
            $table->date('birth_date');
            $table->integer('age_years');
            $table->integer('age_months');
            $table->integer('age_days');
            $table->string('connect_landline')->nullable();
            $table->string('connect_mobile')->nullable();
            $table->string('connect_email')->nullable();
            $table->uuid('health_id');
            $table->uuid('marital_status_id');
            $table->uuid('arm_id');
            $table->uuid('ama_id');
            $table->string('tamin');
            $table->jsonb('khebra')->nullable();
            $table->uuid('education_id');
            $table->year('education_year');
            $table->uuid('stage_id');
            $table->uuid('result_id');
            $table->jsonb('message')->nullable();
            $table->uuid('driver_degree')->nullable();
            $table->date('driver_start')->nullable();
            $table->date('driver_end')->nullable();
            $table->string('file_name', 255);
            $table->dates();
            // إضافة قيد فريد على العمودين annonce_id و nid
            $table->unique(['annonce_id', 'nid']);
        });
        Schema::create('employment_people_degrees', function (Blueprint $table) {
            $table->uid();
            $table->uuid('people_id')->cascadeOnDelete()->cascadeonUpdate();
            $table->float('editorial', 1, 4)->nullable();
            $table->float('practical', 1, 4)->nullable();
            $table->float('interview', 1, 4)->nullable();
            $table->dates();
        });
        Schema::create('employment_people_new_data', function (Blueprint $table) {
            $table->uid();
            $table->uuid('people_id');
            $table->uuid('job_id');
            $table->string('fname');
            $table->string('sname');
            $table->string('tname');
            $table->string('lname');
            $table->uuid('live_gov');
            $table->uuid('live_city');
            $table->mediumText('live_address');
            $table->uuid('born_gov');
            $table->uuid('born_city');
            $table->string('connect_landline')->nullable();
            $table->string('connect_mobile')->nullable();
            $table->string('connect_email')->nullable();
            $table->uuid('health_id');
            $table->uuid('marital_status_id');
            $table->uuid('arm_id');
            $table->uuid('ama_id');
            $table->string('tamin');
            $table->string('tamin')->nullable();
            $table->uuid('education_id');
            $table->year('education_year');
            $table->uuid('stage_id');
            $table->uuid('result_id');
            $table->json('message')->nullable();
            $table->uuid('driver_degree')->nullable();
            $table->date('driver_start')->nullable();
            $table->date('driver_end')->nullable();
            $table->string('file_name', 255);
            $table->dates();
        });
        Schema::create('employment_people_new_stage', function (Blueprint $table) {
            $table->uid();
            $table->uuid('people_id');
            $table->uuid('status_id');
            $table->longText('message')->nullable();
            $table->uuid('stage_id');
            $table->dates();
        });
        Schema::create('employment_qualifications', function (Blueprint $table) {
            $table->uid();
            $table->enum('type', ['public', 'private'])->default('public');
            $table->longText('text')->unique();
            $table->dates();
        });
        Schema::create('employment_seatings', function (Blueprint $table) {
            $table->uid();
            $table->uuid('people_id');
            $table->uuid('stage_id');
            $table->integer('number')->nullable();
            $table->uuid('committee_id');
            $table->dateTimeTz('Committee_date')->nullable();
            $table->dates();
        });
        Schema::create('employment_stages', function (Blueprint $table) {
            $table->uid();
            $table->string('text');
            $table->integer('days')->nullable();
            $table->string('page')->nullable();
            $table->enum('front', [0, 1])->default(0)->nullable();
            $table->dates();
        });
        Schema::create('employment_start_annonces', function (Blueprint $table) {
            $table->uid();
            $table->integer('number');
            $table->integer('year');
            $table->mediumText('description')->nullable();
            $table->uuid('stage_id');
            $table->string('slug');
            $table->enum('status', ['publish', 'draft'])->default('draft')->nullable();
            $table->dates();
        });
        Schema::create('employment_static_pages', function (Blueprint $table) {
            $table->uid();
            $table->string('name');
            $table->longText('content')->nullable();
            $table->text('data')->nullable();
            $table->dates();
        });
        Schema::create('employment_status', function (Blueprint $table) {
            $table->uid();
            $table->string('text')->unique();
            $table->dates();
        });

        Schema::create('employment_committees_person', function (Blueprint $table) {
            $table->uid();
            $table->uuid('committee_id');
            $table->uuid('person_id');
            $table->dates();
        });

        Schema::create('employment_jobs_ama', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('ama_id');
            $table->dates();
        });
        Schema::create('employment_jobs_army', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('arm_id');
            $table->dates();
        });
        Schema::create('employment_jobs_city', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('city_id');
            $table->dates();
        });
        Schema::create('employment_jobs_driver', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('driver_id');
            $table->dates();
        });
        Schema::create('employment_jobs_educations', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('education_id');
            $table->dates();
        });
        Schema::create('employment_jobs_health', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('health_id');
            $table->dates();
        });
        Schema::create('employment_jobs_included_files', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('included_file_id');
            $table->dates();
        });
        Schema::create('employment_jobs_instructions', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('instruction_id');
            $table->dates();

        });
        Schema::create('employment_jobs_marital_status', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('marital_status_id');
            $table->dates();

        });
        Schema::create('employment_jobs_org_stru_mahta', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('mahata_id');
            $table->dates();
        });
        Schema::create('employment_jobs_org_stru_area', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('area_id');
            $table->dates();
        });
        Schema::create('employment_jobs_org_stru_section', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('section_id');
            $table->dates();
        });
        Schema::create('employment_jobs_qualification', function (Blueprint $table) {
            $table->uid();
            $table->uuid('job_id');
            $table->uuid('qualification_id');
            $table->dates();

        });
        Schema::create('employment_start_annonce_gov', function (Blueprint $table) {
            $table->uid();
            $table->uuid('annonce_id');
            $table->uuid('governorate_id');
            $table->dates();
        });
        Schema::create('employment_start_annonce_qualification', function (Blueprint $table) {
            $table->uid();
            $table->uuid('annonce_id');
            $table->uuid('qualification_id');
            $table->dates();
        });

        $this->PrimaryIndex();
    }
    public function PrimaryIndex(){
        Schema::table('employment_start_annonces', function (Blueprint $table) {
            $table->foreign('stage_id')->references('id')->on('employment_stages')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs', function (Blueprint $table) {
            $table->foreign('annonce_id')->references('id')->on('employment_start_annonces')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('job_name_id')->references('id')->on('mosama_job_names')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('group_id')->references('id')->on('mosama_groups')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('job_title_id')->references('id')->on('mosama_job_titles')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_people', function (Blueprint $table) {
            $table->foreign('annonce_id')->references('id')->on('employment_start_annonces')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('live_gov')->references('id')->on('governorates')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('live_city')->references('id')->on('cities')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('born_gov')->references('id')->on('governorates')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('born_city')->references('id')->on('cities')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('health_id')->references('id')->on('employment_health')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('marital_status_id')->references('id')->on('employment_marital_status')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('arm_id')->references('id')->on('employment_armies')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('ama_id')->references('id')->on('employment_amas')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('education_id')->references('id')->on('mosama_educations')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('stage_id')->references('id')->on('employment_stages')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('result_id')->references('id')->on('employment_status')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('driver_degree')->references('id')->on('employment_drivers')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_people_new_data', function (Blueprint $table) {
            $table->foreign('people_id')->references('id')->on('employment_people')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('live_gov')->references('id')->on('governorates')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('live_city')->references('id')->on('cities')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('born_gov')->references('id')->on('governorates')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('born_city')->references('id')->on('cities')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('health_id')->references('id')->on('employment_health')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('marital_status_id')->references('id')->on('employment_marital_status')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('arm_id')->references('id')->on('employment_armies')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('ama_id')->references('id')->on('employment_amas')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('education_id')->references('id')->on('mosama_educations')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('stage_id')->references('id')->on('employment_people_new_stage')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('driver_degree')->references('id')->on('employment_drivers')->cascadeOnDelete()->cascadeonUpdate()->nullable();
            $table->foreign('result_id')->references('id')->on('employment_status')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_people_degrees', function (Blueprint $table) {
            $table->foreign('people_id')->references('id')->on('employment_people')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_people_new_stage', function (Blueprint $table) {
            $table->foreign('people_id')->references('id')->on('employment_people')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('status_id')->references('id')->on('employment_status')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('stage_id')->references('id')->on('employment_stages')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_ama', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('ama_id')->references('id')->on('employment_amas')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_army', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('arm_id')->references('id')->on('employment_armies')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_city', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_driver', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('driver_id')->references('id')->on('employment_drivers')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_educations', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('education_id')->references('id')->on('mosama_educations')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_health', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('health_id')->references('id')->on('employment_health')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_included_files', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('included_file_id')->references('id')->on('employment_included_files')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_instructions', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('instruction_id')->references('id')->on('employment_instructions')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_marital_status', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('marital_status_id')->references('id')->on('employment_marital_status')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_org_stru_mahta', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('mahata_id')->references('id')->on('org_stru_mahatas')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_org_stru_area', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('area_id')->references('id')->on('org_stru_areas')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_jobs_org_stru_section', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('section_id')->references('id')->on('org_stru_sections')->cascadeOnDelete()->cascadeonUpdate();
        });

        Schema::table('employment_jobs_qualification', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('employment_jobs')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('qualification_id')->references('id')->on('employment_qualifications')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_committees', function (Blueprint $table) {
            $table->foreign('annonce_id')->references('id')->on('employment_start_annonces')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_committees_person', function (Blueprint $table) {
            $table->foreign('committee_id')->references('id')->on('employment_committees')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('person_id')->references('id')->on('employment_committees_persons')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_start_annonce_gov', function (Blueprint $table) {
            $table->foreign('annonce_id')->references('id')->on('employment_start_annonces')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('governorate_id')->references('id')->on('governorates')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_start_annonce_qualification', function (Blueprint $table) {
            $table->foreign('annonce_id')->references('id')->on('employment_start_annonces')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('qualification_id')->references('id')->on('employment_qualifications')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_grievance', function (Blueprint $table) {
            $table->foreign('people_id')->references('id')->on('employment_people')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('stage_id')->references('id')->on('employment_people_new_stage')->cascadeOnDelete()->cascadeonUpdate();
        });
        Schema::table('employment_annonces', function (Blueprint $table) {
            $table->foreign('annonce_id')->references('id')->on('employment_start_annonces')->cascadeOnDelete()->cascadeonUpdate();
        });

        Schema::table('employment_seatings', function (Blueprint $table) {
            $table->foreign('people_id')->references('id')->on('employment_people')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('stage_id')->references('id')->on('employment_people_new_stage')->cascadeOnDelete()->cascadeonUpdate();
            $table->foreign('committee_id')->references('id')->on('employment_committees')->cascadeOnDelete()->cascadeonUpdate();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('employment_amas');
        Schema::dropIfExists('employment_armies');
        Schema::dropIfExists('employment_drivers');
        Schema::dropIfExists('employment_health');
        Schema::dropIfExists('employment_included_files');
        Schema::dropIfExists('employment_instructions');
        Schema::dropIfExists('employment_jobs');
        Schema::dropIfExists('employment_jobs_ama');
        Schema::dropIfExists('employment_jobs_army');
        Schema::dropIfExists('employment_jobs_city');
        Schema::dropIfExists('employment_jobs_driver');
        Schema::dropIfExists('employment_jobs_educations');
        Schema::dropIfExists('employment_jobs_health');
        Schema::dropIfExists('employment_jobs_included_files');
        Schema::dropIfExists('employment_jobs_instructions');
        Schema::dropIfExists('employment_jobs_marital_status');
        Schema::dropIfExists('employment_jobs_org_stru_mahta');
        Schema::dropIfExists('employment_jobs_qualification');
        Schema::dropIfExists('employment_marital_status');
        Schema::dropIfExists('employment_committees_persons');
        Schema::dropIfExists('employment_committees');
        Schema::dropIfExists('employment_dinamic_pages');
        Schema::dropIfExists('employment_static_pages');
        Schema::dropIfExists('employment_people');
        Schema::dropIfExists('employment_people_new_data');
        Schema::dropIfExists('employment_people_degrees');
        Schema::dropIfExists('employment_people_new_stage');
        Schema::dropIfExists('employment_stages');
        Schema::dropIfExists('employment_start_annonces');
        Schema::dropIfExists('employment_start_annonce_gov');
        Schema::dropIfExists('employment_start_annonce_qualification');
        Schema::dropIfExists('employment_qualifications');
        Schema::dropIfExists('employment_status');
        Schema::dropIfExists('employment_grievance');

    }
};
