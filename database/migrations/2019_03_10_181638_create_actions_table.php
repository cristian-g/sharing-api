<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Action;
use App\Outgo;
use App\OutgoCategory;
use App\Payment;
use App\User;
use App\Vehicle;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->uuid('id'); $table->primary('id');

            // Vehicle
            $table->uuid('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');

            // Outgo
            $table->uuid('outgo_id')->nullable();
            $table->foreign('outgo_id')->references('id')->on('outgoes');

            // Payment
            $table->uuid('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments');

            $table->timestamps();
        });

        // Sign up main users
        $main_user1 = User::create([
            'name' => 'Albert',
            'surnames' => '',
            'email' => 'usuario1test@cristiangonzalez.com',
            'auth0id' => 'sample_id2',
            'password' => null,
        ]);
        $main_user2 = User::create([
            'name' => 'Àngela',
            'surnames' => '',
            'email' => 'angela.brunet@cristiangonzalez.com',
            'auth0id' => 'sample_id1',
            'password' => null,
        ]);
        $main_user3 = User::create([
            'name' => 'Cristian',
            'surnames' => '',
            'email' => 'cristian@cristiangonzalez.com',
            'auth0id' => 'sample_id5',
            'password' => null,
        ]);

        // Sign up auxiliar users
        $aux_user1 = $this->signup1();
        $aux_user2 = $this->signup2();
        $aux_user3 = $this->signup3();

        // Sign up vehicle
        //$first_vehicle = Vehicle::first();
        $first_vehicle = Vehicle::create([
            'brand' => 'Toyota',
            'model' => 'Prius',
        ]);

        // Attach users
        $first_vehicle->users()->attach($main_user1, [
            'is_owner' => true,
        ]);
        $first_vehicle->users()->attach($main_user2, [
            'is_owner' => false,
        ]);
        $first_vehicle->users()->attach($main_user3, [
            'is_owner' => false,
        ]);
        /*$first_vehicle->users()->attach($aux_user1, [
            'is_owner' => false,
        ]);
        $first_vehicle->users()->attach($aux_user2, [
            'is_owner' => false,
        ]);
        $first_vehicle->users()->attach($aux_user3, [
            'is_owner' => false,
        ]);*/

        // Fill with outgoes
        $this->fake0($main_user1);// user1
        $this->fake1($main_user2);// user2
        $this->fake2($main_user3);// user3

        $receiver = $main_user1;
        $this->fake3($receiver, $main_user3);// user2
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }

    /**
     * Fake 0
     *
     * @return \Illuminate\Http\Response
     */
    private function fake0($user)
    {
        $vehicle = $user->vehicles()->first();

        $gasPrice = 1.25;

        $liters = 0.3;
        $quantity = $liters * $gasPrice;
        $description = 'Consumo de ' . $liters . ' litros * ' . $gasPrice . ' €/litro = ' . $quantity . ' €';

        $outgo = new Outgo([
            'quantity' => $quantity,
            'description' => $description,
            'gas_liters' => 19.4,
            //'notes' => $request->notes, // only add them if filled in request!
            //'share_outgo' => $request->share_outgo, // only add them if filled in request!
            //'points' => $request->points, // only add them if filled in request!
        ]);

        $outgoCategory = OutgoCategory::where([
            'key_name' => 'drive'
        ])->first();

        $outgo->vehicle()->associate($vehicle);
        $outgo->user()->associate($user);
        $outgo->outgoCategory()->associate($outgoCategory);

        $outgo->save();

        $original_outgo = $outgo;

        $action = new Action([
        ]);

        $action->outgo_id = $outgo->id;
        $action->vehicle()->associate($vehicle);
        $action->save();

        // Distribute the outgo to current existing users
        $users = $vehicle->users()->get();
        $n_users = sizeof($users);
        foreach ($users as $aux_user) {
            $outgo = new Outgo([
                'quantity' => (abs($quantity)) / $n_users,
                'description' => ($description == null) ? "" : $description,
                'notes' => "",
                'share_outgo' => true,
                'points' => abs($quantity) * 100,
            ]);
            $outgo->vehicle()->associate($vehicle);
            $outgo->user()->associate($user);
            $outgo->receiver()->associate($aux_user);
            $outgo->originalOutgo()->associate($original_outgo);
            $outgo->outgoCategory()->associate($outgoCategory);
            $outgo->save();
        }

        return response()->json(['success' => true], 200);
    }

    /**
     * Fake 1
     *
     * @return \Illuminate\Http\Response
     */
    private function fake1($user)
    {
        $vehicle = $user->vehicles()->first();

        $gasPrice = 1.25;

        $liters = 1.2;
        $quantity = $liters * $gasPrice;
        $description = 'Consumo de ' . $liters . ' litros * ' . $gasPrice . ' €/litro = ' . $quantity . ' €';

        $outgo = new Outgo([
            'quantity' => $quantity,
            'description' => $description,
            'gas_liters' => 36.4,
            //'notes' => $request->notes, // only add them if filled in request!
            //'share_outgo' => $request->share_outgo, // only add them if filled in request!
            //'points' => $request->points, // only add them if filled in request!
            'created_at' => '2019-09-01 17:22:56',
        ]);

        $outgoCategory = OutgoCategory::where([
            'key_name' => 'drive'
        ])->first();

        $outgo->vehicle()->associate($vehicle);
        $outgo->user()->associate($user);
        $outgo->outgoCategory()->associate($outgoCategory);

        $outgo->save();

        $original_outgo = $outgo;

        $action = new Action([
            'created_at' => '2019-09-01 17:22:56',
        ]);

        $action->outgo_id = $outgo->id;
        $action->vehicle()->associate($vehicle);
        $action->save();

        // Distribute the outgo to current existing users
        $users = $vehicle->users()->get();
        $n_users = sizeof($users);
        foreach ($users as $aux_user) {
            $outgo = new Outgo([
                'quantity' => (abs($quantity)) / $n_users,
                'description' => ($description == null) ? "" : $description,
                'notes' => "",
                'share_outgo' => true,
                'points' => abs($quantity) * 100,
                'created_at' => '2019-09-01 17:22:56',
            ]);
            $outgo->vehicle()->associate($vehicle);
            $outgo->user()->associate($user);
            $outgo->receiver()->associate($aux_user);
            $outgo->originalOutgo()->associate($original_outgo);
            $outgo->outgoCategory()->associate($outgoCategory);
            $outgo->save();
        }

        return response()->json(['success' => true], 200);
    }

    /**
     * Fake 2
     *
     * @return \Illuminate\Http\Response
     */
    private function fake2($user)
    {
        $vehicle = $user->vehicles()->first();

        $gasPrice = 1.25;

        $liters = 50;
        $quantity = 40.0;
        $description = 'Reparación ventanillas';

        $outgo = new Outgo([
            'quantity' => $quantity * (-1),
            'description' => $description,
            'gas_liters' => 0,
            'created_at' => '2019-09-02 12:33:56',
        ]);

        $outgoCategory = OutgoCategory::where([
            'key_name' => 'drive'
        ])->first();

        $outgo->vehicle()->associate($vehicle);
        $outgo->user()->associate($user);
        $outgo->outgoCategory()->associate($outgoCategory);

        $outgo->save();

        $original_outgo = $outgo;

        $action = new Action([
            'created_at' => '2019-09-02 12:33:56',
        ]);

        $action->outgo_id = $outgo->id;
        $action->vehicle()->associate($vehicle);
        $action->save();

        // Distribute the outgo to current existing users
        $users = $vehicle->users()->get();
        $n_users = sizeof($users);
        foreach ($users as $aux_user) {
            $outgo = new Outgo([
                'quantity' => ($quantity * (-1)) / $n_users,
                'description' => ($description == null) ? "" : $description,
                'notes' => "",
                'share_outgo' => true,
                'points' => abs($quantity) * 100,
                'created_at' => '2019-09-02 12:33:56',
            ]);
            $outgo->vehicle()->associate($vehicle);
            $outgo->user()->associate($user);
            $outgo->receiver()->associate($aux_user);
            $outgo->originalOutgo()->associate($original_outgo);
            $outgo->outgoCategory()->associate($outgoCategory);
            $outgo->save();
        }

        return response()->json(['success' => true], 200);
    }

    /**
     * Fake 3
     *
     * @return \Illuminate\Http\Response
     */
    private function fake3($user, $receiver)
    {
        $vehicle = $user->vehicles()->first();
        $vehicle_id = $vehicle->id;

        $payment_quantity = 5.0;

        $vehicle = Vehicle::where([
            "id" => $vehicle_id,
        ])->first();


        $action = new Action([
            'created_at' => '2019-09-03 07:23:56',
        ]);

        $payment = new Payment([
            'quantity' => $payment_quantity,
            'created_at' => '2019-09-03 07:23:56',
        ]);

        $payment->vehicle()->associate($vehicle);
        $payment->user()->associate($user);

        $payment->receiver()->associate($receiver);

        $payment->save();

        $action->payment_id = $payment->id;
        $action->vehicle()->associate($vehicle);
        $action->save();

        return response()->json(['success' => true], 200);
    }

    private function signup1()
    {
        $user = User::create([
            'name' => 'Usuario 1',
            'surnames' => '',
            'email' => 'usuario1testing@cristiangonzalez.com',
            'auth0id' => 'usuario1test',
            'password' => null,
        ]);
        //return response()->json(['success' => true], 200);
        return $user;
    }

    private function signup2()
    {
        $user = User::create([
            'name' => 'Usuario 2',
            'surnames' => '',
            'email' => 'usuario2testing@cristiangonzalez.com',
            'auth0id' => 'usuario2test',
            'password' => null,
        ]);
        //return response()->json(['success' => true], 200);
        return $user;
    }

    private function signup3()
    {
        $user = User::create([
            'name' => 'Usuario 3',
            'surnames' => '',
            'email' => 'usuario3testing@cristiangonzalez.com',
            'auth0id' => 'usuario3test',
            'password' => null,
        ]);
        //return response()->json(['success' => true], 200);
        return $user;
    }
}
