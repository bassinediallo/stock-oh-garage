<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->date('date_verification');
            
            // Zone de travail et Atelier
            $table->string('ponts_elevateurs')->nullable();
            $table->text('ponts_elevateurs_remarques')->nullable();
            $table->string('ecran_tv')->nullable();
            $table->text('ecran_tv_remarques')->nullable();
            $table->string('lampes')->nullable();
            $table->text('lampes_remarques')->nullable();
            $table->string('extincteur')->nullable();
            $table->text('extincteur_remarques')->nullable();
            $table->string('cameras')->nullable();
            $table->text('cameras_remarques')->nullable();
            $table->string('telephones')->nullable();
            $table->text('telephones_remarques')->nullable();
            $table->string('ordinateurs')->nullable();
            $table->text('ordinateurs_remarques')->nullable();
            $table->string('imprimante')->nullable();
            $table->text('imprimante_remarques')->nullable();
            $table->string('fontaine_eau')->nullable();
            $table->text('fontaine_eau_remarques')->nullable();
            $table->string('cafe_equipements')->nullable();
            $table->text('cafe_equipements_remarques')->nullable();
            $table->string('magazines')->nullable();
            $table->text('magazines_remarques')->nullable();
            $table->string('odeur_generale')->nullable();
            $table->text('odeur_generale_remarques')->nullable();
            $table->string('proprete_generale')->nullable();
            $table->text('proprete_generale_remarques')->nullable();
            
            // Zone technique
            $table->string('cuve_huile')->nullable();
            $table->text('cuve_huile_remarques')->nullable();
            $table->string('niveau_cuves')->nullable();
            $table->text('niveau_cuves_remarques')->nullable();
            $table->string('proprete_technique')->nullable();
            $table->text('proprete_technique_remarques')->nullable();
            $table->string('pieces_moteurs')->nullable();
            $table->text('pieces_moteurs_remarques')->nullable();
            
            // Parking et Façade
            $table->string('proprete_parking')->nullable();
            $table->text('proprete_parking_remarques')->nullable();
            $table->string('etat_voitures')->nullable();
            $table->text('etat_voitures_remarques')->nullable();
            $table->string('etat_jardin')->nullable();
            $table->text('etat_jardin_remarques')->nullable();
            $table->string('agent_securite')->nullable();
            $table->text('agent_securite_remarques')->nullable();
            $table->string('facade_vitres')->nullable();
            $table->text('facade_vitres_remarques')->nullable();
            $table->string('enseigne')->nullable();
            $table->text('enseigne_remarques')->nullable();
            $table->string('signalisation')->nullable();
            $table->text('signalisation_remarques')->nullable();
            $table->string('parcours_client')->nullable();
            $table->text('parcours_client_remarques')->nullable();
            
            // Contrôle final
            $table->string('proprete_finale')->nullable();
            $table->string('securite_finale')->nullable();
            $table->text('materiel_manquant')->nullable();
            $table->text('recommandations')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checklists');
    }
};
