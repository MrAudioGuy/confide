<?php echo "<?php\n"; ?>

	use Illuminate\Database\Migrations\Migration;

	class ConfideSetupUsersTable extends Migration
	{
		/**
		 * Run the migrations.
		 */
		public function up()
		{
			// Creates the {{ $table }} table
			Schema::create('{{ $table }}', function ($table)
			{
				$table->bigIncrements('id')->unsigned();
				$table->string('username')->unique();
				$table->string('email')->unique();
				$table->string('mobile')->unique();
				$table->string('password');
				$table->string('secret');
				$table->string('remember_token')->nullable();
				$table->integer('status')->default(1);
				$table->timestamps();
				$table->softDeletes();
			});

			// Creates password reminders table
			Schema::create('reminders', function ($table)
			{
				$table->bigIncrements('id')->unsigned();
				$table->string('email')->nullable();
				$table->string('mobile')->nullable();
				$table->bigInteger('user_id')->unsigned()->nullable();
				$table->string('type')->nullable();
				$table->string('token')->unique();
				$table->timestamps();
				$table->softDeletes();

				$table->foreign('user_id')
					  ->references('id')->on('{{ $table }}')
					  ->onDelete('cascade')
					  ->onUpdate('cascade');
			});

			// Create list of approved oath providers
			Schema::create('oathproviders', function ($table)
			{
				$table->bigIncrements('id')->unsigned();
				$table->string('provider');
				$table->string('allowed_services');
				$table->string('url');
				$table->timestamps();
				$table->softDeletes();
			});

			// Create OAuth specific features
			Schema::create('oath', function ($table)
			{
				$table->bigIncrements('id')->unsigned();
				$table->bigInteger('user_id')->unsigned();
				$table->bigInteger('provider_id')->unsigned();
				$table->string('services');
				$table->string('token');
				$table->timestamps();
				$table->softDeletes();

				$table->foreign('user_id')
					  ->references('id')->on('{{ $table }}')
					  ->onDelete('cascade')
					  ->onUpdate('cascade');

				$table->foreign('provider_id')
					  ->references('id')->on('oathproviders')
					  ->onDelete('cascade')
					  ->onUpdate('cascade');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down()
		{
			Schema::table('reminders', function($table){
				$table->dropForeign('reminders_user_id_foreign');
			});
			Schema::table('oath', function($table)
			{
				$table->dropForeign('oath_user_id_foreign');
				$table->dropForeign('oath_provider_foreign');
			});

			Schema::drop('oathproviders');
			Schema::drop('oath');
			Schema::drop('reminders');
			Schema::drop('{{ $table }}');
		}
	}