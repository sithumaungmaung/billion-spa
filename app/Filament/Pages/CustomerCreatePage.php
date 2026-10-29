<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\CustomerInfo;
use App\Models\User;
use BackedEnum, UnitEnum;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class CustomerCreatePage extends Page
{
    protected string $view = 'filament.pages.customer-create-page';

    protected static string | UnitEnum | null $navigationGroup = 'Admin, Therapists & Customers ';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    // protected static ?string $recordTitleAttribute = 'Admin';

    protected static ?string $navigationLabel = 'Customers';

    protected static ?int $navigationSort = 3;

    // ------------------------------------------------------

    public $name = '';
    public $phone = '';
    public $selectedBranch = null;
    public $date_of_birth = '';

    public $branches = [];

    protected $uniqueCustomerID = '';


    public function mount(): void
    {
        $this->getBranches();
    }

    public function createCustomer(): void
    {
        $this->autoGenerateEmailAndPassword();


        DB::transaction(function () {
            $customerCreation = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'email_verified_at' => Carbon::now()
            ]);

            $customerCreation->assignRole('customer');

            if($customerCreation)
            {
                CustomerInfo::create([
                    'customer_id' => $this->uniqueCustomerID,
                    'user_id' => $customerCreation->id,
                    'branch_id' => $this->selectedBranch,
                    'phone' => $this->phone,
                    'date_of_birth' => $this->date_of_birth ? Carbon::parse($this->date_of_birth) : null,
                ]);
            }
        });

    }


    public function getBranches(): void
    {
        $this->branches = Branch::all();
    }


    public function getCustomerList()
    {
        return User::role('customer')->with('customerInfo')->paginate(20);
    }

    // ======================================        ========================================

    private function autoGenerateEmailAndPassword(): void
    {
        $email  = "CUSTOMER_".str_replace(' ', '_', $this->name)."_".substr(time(), 0, 5)."@gmail.com";
        $password = Hash::make($email);
        $uniqueCustomerID = 'CUS-'
            . substr(md5(uniqid('', true)), 0, 6) // random 6 chars
            . substr((string) time(), 0, 5);      // first 5 digits of timestamp
        $this->email = $email;
        $this->password = $password;
        $this->uniqueCustomerID = $uniqueCustomerID;
    }







}