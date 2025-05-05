Schema::create('product_news', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_template_id')->constrained()->onDelete('cascade');
    $table->integer('qty')->default(0);
    $table->float('price');
    $table->float('discount_price')->nullable();
    $table->boolean('most_popular')->default(false);
    $table->boolean('best_seller')->default(false);
    $table->string('status')->default('active');
    $table->timestamps();
});


Schema::create('product_templates', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->nullable();
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->foreignId('menu_id')->nullable()->constrained()->onDelete('set null');
    $table->string('code')->nullable();
    $table->string('size')->nullable();
    $table->string('unit')->nullable();
    $table->string('image')->nullable();
    $table->timestamps();
});
