<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BangladeshLocationSeeder extends Seeder
{
    public function run(): void
    {
        if (District::count() > 0) {
            return;
        }

        $districts = [
            // Dhaka Division (13)
            ['name' => 'Dhaka', 'bn_name' => 'ঢাকা', 'delivery_charge' => 60.00, 'thanas' => [
                ['name' => 'Gulshan', 'bn_name' => 'গুলশান', 'areas' => [
                    ['name' => 'Gulshan 1', 'postal_code' => '1212'],
                    ['name' => 'Gulshan 2', 'postal_code' => '1212'],
                    ['name' => 'Niketan', 'postal_code' => '1212'],
                    ['name' => 'Baridhara Diplomatic Zone', 'postal_code' => '1212'],
                ]],
                ['name' => 'Banani', 'bn_name' => 'বনানী', 'areas' => [
                    ['name' => 'Banani DOHS', 'postal_code' => '1206'],
                    ['name' => 'Banani Chairman Bari', 'postal_code' => '1213'],
                    ['name' => 'Banani Road 11', 'postal_code' => '1213'],
                ]],
                ['name' => 'Dhanmondi', 'bn_name' => 'ধানমন্ডি', 'areas' => [
                    ['name' => 'Dhanmondi Road 27', 'postal_code' => '1209'],
                    ['name' => 'Dhanmondi Road 15', 'postal_code' => '1209'],
                    ['name' => 'Jigatola', 'postal_code' => '1209'],
                    ['name' => 'Kalabagan', 'postal_code' => '1205'],
                ]],
                ['name' => 'Uttara', 'bn_name' => 'উত্তরা', 'areas' => [
                    ['name' => 'Uttara Sector 1', 'postal_code' => '1230'],
                    ['name' => 'Uttara Sector 3', 'postal_code' => '1230'],
                    ['name' => 'Uttara Sector 7', 'postal_code' => '1230'],
                    ['name' => 'Uttara Sector 11', 'postal_code' => '1230'],
                ]],
                ['name' => 'Mirpur', 'bn_name' => 'মিরপুর', 'areas' => [
                    ['name' => 'Mirpur 1', 'postal_code' => '1216'],
                    ['name' => 'Mirpur 10', 'postal_code' => '1216'],
                    ['name' => 'Mirpur DOHS', 'postal_code' => '1216'],
                    ['name' => 'Pallabi', 'postal_code' => '1216'],
                ]],
                ['name' => 'Mohammadpur', 'bn_name' => 'মোহাম্মদপুর', 'areas' => [
                    ['name' => 'Lalmatia', 'postal_code' => '1207'],
                    ['name' => 'Tajmahal Road', 'postal_code' => '1207'],
                    ['name' => 'Adabor', 'postal_code' => '1207'],
                ]],
                ['name' => 'Motijheel', 'bn_name' => 'মতিঝিল', 'areas' => [
                    ['name' => 'Motijheel Commercial Area', 'postal_code' => '1000'],
                    ['name' => 'Arambagh', 'postal_code' => '1000'],
                    ['name' => 'Fakirapool', 'postal_code' => '1000'],
                ]],
                ['name' => 'Badda', 'bn_name' => 'বাড্ডা', 'areas' => [
                    ['name' => 'Middle Badda', 'postal_code' => '1212'],
                    ['name' => 'North Badda', 'postal_code' => '1212'],
                    ['name' => 'Aftabnagar', 'postal_code' => '1212'],
                ]],
                ['name' => 'Tejgaon', 'bn_name' => 'তেজগাঁও', 'areas' => [
                    ['name' => 'Tejgaon Industrial Area', 'postal_code' => '1208'],
                    ['name' => 'Farmgate', 'postal_code' => '1215'],
                    ['name' => 'Karwan Bazar', 'postal_code' => '1215'],
                ]],
                ['name' => 'Savar', 'bn_name' => 'সাভার', 'areas' => [
                    ['name' => 'Savar Bazar', 'postal_code' => '1340'],
                    ['name' => 'Ashulia', 'postal_code' => '1341'],
                ]],
            ]],
            ['name' => 'Chattogram', 'bn_name' => 'চট্টগ্রাম', 'delivery_charge' => 100.00, 'thanas' => [
                ['name' => 'Panchlaish', 'bn_name' => 'পাঁচলাইশ', 'areas' => [
                    ['name' => 'Nasirabad', 'postal_code' => '4000'],
                    ['name' => 'GEC Circle', 'postal_code' => '4000'],
                ]],
                ['name' => 'Kotwali', 'bn_name' => 'কোতোয়ালী', 'areas' => [
                    ['name' => 'Anderkilla', 'postal_code' => '4000'],
                    ['name' => 'Agrabad', 'postal_code' => '4100'],
                ]],
                ['name' => 'Khulshi', 'bn_name' => 'খুলশী', 'areas' => [
                    ['name' => 'South Khulshi', 'postal_code' => '4225'],
                    ['name' => 'Zakir Hossain Road', 'postal_code' => '4225'],
                ]],
            ]],
            ['name' => 'Gazipur', 'bn_name' => 'গাজীপুর', 'delivery_charge' => 90.00, 'thanas' => [
                ['name' => 'Gazipur Sadar', 'bn_name' => 'গাজীপুর সদর', 'areas' => [['name' => 'Joydebpur', 'postal_code' => '1700'], ['name' => 'Chowrasta', 'postal_code' => '1702']]],
                ['name' => 'Tongi', 'bn_name' => 'টঙ্গী', 'areas' => [['name' => 'Tongi Bazar', 'postal_code' => '1710'], ['name' => 'College Gate', 'postal_code' => '1712']]],
            ]],
            ['name' => 'Narayanganj', 'bn_name' => 'নারায়ণগঞ্জ', 'delivery_charge' => 80.00, 'thanas' => [
                ['name' => 'Narayanganj Sadar', 'bn_name' => 'নারায়ণগঞ্জ সদর', 'areas' => [['name' => 'Chashara', 'postal_code' => '1400'], ['name' => 'Fatullah', 'postal_code' => '1420']]],
                ['name' => 'Siddhirganj', 'bn_name' => 'সিদ্ধিরগঞ্জ', 'areas' => [['name' => 'Signboard', 'postal_code' => '1430']]],
            ]],
            ['name' => 'Sylhet', 'bn_name' => 'সিলেট', 'delivery_charge' => 120.00, 'thanas' => [
                ['name' => 'Sylhet Sadar', 'bn_name' => 'সিলেট সদর', 'areas' => [['name' => 'Zindabazar', 'postal_code' => '3100'], ['name' => 'Amberkhana', 'postal_code' => '3100'], ['name' => 'Shahjalal Upashahar', 'postal_code' => '3100']]],
            ]],
            ['name' => 'Rajshahi', 'bn_name' => 'রাজশাহী', 'delivery_charge' => 120.00, 'thanas' => [
                ['name' => 'Boalia', 'bn_name' => 'বোয়ালিয়া', 'areas' => [['name' => 'Shaheb Bazar', 'postal_code' => '6100'], ['name' => 'Uposhohor', 'postal_code' => '6202']]],
            ]],
            ['name' => 'Khulna', 'bn_name' => 'খুলনা', 'delivery_charge' => 120.00, 'thanas' => [
                ['name' => 'Khulna Sadar', 'bn_name' => 'খুলনা সদর', 'areas' => [['name' => 'Shib Bari', 'postal_code' => '9100'], ['name' => 'Sonadanga', 'postal_code' => '9100']]],
            ]],
            ['name' => 'Barishal', 'bn_name' => 'বরিশাল', 'delivery_charge' => 120.00, 'thanas' => [
                ['name' => 'Barishal Sadar', 'bn_name' => 'বরিশাল সদর', 'areas' => [['name' => 'Band Road', 'postal_code' => '8200'], ['name' => 'Nathullabad', 'postal_code' => '8200']]],
            ]],
            ['name' => 'Rangpur', 'bn_name' => 'রংপুর', 'delivery_charge' => 120.00, 'thanas' => [
                ['name' => 'Rangpur Sadar', 'bn_name' => 'রংপুর সদর', 'areas' => [['name' => 'Jahaj Company More', 'postal_code' => '5400'], ['name' => 'Dhap', 'postal_code' => '5400']]],
            ]],
            ['name' => 'Mymensingh', 'bn_name' => 'ময়মনসিংহ', 'delivery_charge' => 120.00, 'thanas' => [
                ['name' => 'Mymensingh Sadar', 'bn_name' => 'ময়মনসিংহ সদর', 'areas' => [['name' => 'Ganginarpar', 'postal_code' => '2200'], ['name' => 'Charpara', 'postal_code' => '2200']]],
            ]],
            ['name' => 'Cumilla', 'bn_name' => 'কুমিল্লা', 'delivery_charge' => 110.00, 'thanas' => [
                ['name' => 'Cumilla Adarsha Sadar', 'bn_name' => 'কুমিল্লা আদর্শ সদর', 'areas' => [['name' => 'Kandirpar', 'postal_code' => '3500'], ['name' => 'Tomsom Bridge', 'postal_code' => '3500']]],
            ]],
            ['name' => "Cox's Bazar", 'bn_name' => 'কক্সবাজার', 'delivery_charge' => 130.00, 'thanas' => [
                ['name' => "Cox's Bazar Sadar", 'bn_name' => 'কক্সবাজার সদর', 'areas' => [['name' => 'Kolatoli', 'postal_code' => '4700'], ['name' => 'Laboni Point', 'postal_code' => '4700']]],
            ]],
        ];

        // Remaining 52 districts of Bangladesh (total 64 districts)
        $otherDistricts = [
            ['Bagerhat', 'বাগেরহাট', '9300'],
            ['Bandarban', 'বান্দরবান', '4600'],
            ['Barguna', 'বরগুনা', '8700'],
            ['Bhola', 'ভোলা', '8300'],
            ['Bogura', 'বগুড়া', '5800'],
            ['Brahmanbaria', 'ব্রাহ্মণবাড়িয়া', '3400'],
            ['Chandpur', 'চাঁদপুর', '3600'],
            ['Chapainawabganj', 'চাঁপাইনবাবগঞ্জ', '6300'],
            ['Chuadanga', 'চুয়াডাঙ্গা', '7200'],
            ['Dinajpur', 'দিনাজপুর', '5200'],
            ['Faridpur', 'ফরিদপুর', '7800'],
            ['Feni', 'ফেনী', '3900'],
            ['Gaibandha', 'গাইবান্ধা', '5700'],
            ['Gopalganj', 'গোপালগঞ্জ', '8100'],
            ['Habiganj', 'হবিগঞ্জ', '3300'],
            ['Jamalpur', 'জামালপুর', '2000'],
            ['Jashore', 'যশোর', '7400'],
            ['Jhalokati', 'ঝালকাঠি', '8400'],
            ['Jhenaidah', 'ঝিনাইদহ', '7300'],
            ['Joypurhat', 'জয়পুরহাট', '5900'],
            ['Khagrachhari', 'খাগড়াছড়ি', '4400'],
            ['Kishoreganj', 'কিশোরগঞ্জ', '2300'],
            ['Kurigram', 'কুড়িগ্রাম', '5600'],
            ['Kushtia', 'কুষ্টিয়া', '7000'],
            ['Lakshmipur', 'লক্ষ্মীপুর', '3700'],
            ['Lalmonirhat', 'লালমনিরহাট', '5500'],
            ['Madaripur', 'মাদারীপুর', '7900'],
            ['Magura', 'মাগুরা', '7600'],
            ['Manikganj', 'মানিকগঞ্জ', '1800'],
            ['Meherpur', 'মেহেরপুর', '7100'],
            ['Moulvibazar', 'মৌলভীবাজার', '3200'],
            ['Munshiganj', 'মুন্সিগঞ্জ', '1500'],
            ['Naogaon', 'নওগাঁ', '6500'],
            ['Narail', 'নড়াইল', '7500'],
            ['Narsingdi', 'নরসিংদী', '1600'],
            ['Natore', 'নাটোর', '6400'],
            ['Netrokona', 'নেত্রকোণা', '2400'],
            ['Nilphamari', 'নীলফামারী', '5300'],
            ['Noakhali', 'নোয়াখালী', '3800'],
            ['Pabna', 'পাবনা', '6600'],
            ['Panchagarh', 'পঞ্চগড়', '5000'],
            ['Patuakhali', 'পটুয়াখালী', '8600'],
            ['Pirojpur', 'পিরোজপুর', '8500'],
            ['Rajbari', 'রাজবাড়ী', '7700'],
            ['Rangamati', 'রাঙ্গামাটি', '4500'],
            ['Satkhira', 'সাতক্ষীরা', '9400'],
            ['Shariatpur', 'শরীয়তপুর', '8000'],
            ['Sherpur', 'শেরপুর', '2100'],
            ['Sirajganj', 'সিরাজগঞ্জ', '6700'],
            ['Sunamganj', 'সুনামগঞ্জ', '3000'],
            ['Tangail', 'টাঙ্গাইল', '1900'],
            ['Thakurgaon', 'ঠাকুরগাঁও', '5100'],
        ];

        foreach ($otherDistricts as [$name, $bnName, $postal]) {
            $districts[] = [
                'name' => $name,
                'bn_name' => $bnName,
                'delivery_charge' => 120.00,
                'thanas' => [
                    [
                        'name' => $name . ' Sadar',
                        'bn_name' => $bnName . ' সদর',
                        'areas' => [
                            ['name' => $name . ' Town', 'postal_code' => $postal],
                            ['name' => $name . ' Station Road', 'postal_code' => $postal],
                        ],
                    ],
                ],
            ];
        }

        foreach ($districts as $dIdx => $dData) {
            $district = District::create([
                'name' => $dData['name'],
                'bn_name' => $dData['bn_name'],
                'slug' => Str::slug($dData['name']),
                'delivery_charge' => $dData['delivery_charge'],
                'sort_order' => $dIdx + 1,
                'is_active' => true,
            ]);

            foreach ($dData['thanas'] as $tIdx => $tData) {
                $thana = Thana::create([
                    'district_id' => $district->id,
                    'name' => $tData['name'],
                    'bn_name' => $tData['bn_name'] ?? null,
                    'slug' => Str::slug($district->name . '-' . $tData['name']),
                    'sort_order' => $tIdx + 1,
                    'is_active' => true,
                ]);

                foreach ($tData['areas'] as $aIdx => $aData) {
                    Area::create([
                        'district_id' => $district->id,
                        'thana_id' => $thana->id,
                        'name' => $aData['name'],
                        'bn_name' => $aData['bn_name'] ?? null,
                        'postal_code' => $aData['postal_code'] ?? null,
                        'sort_order' => $aIdx + 1,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}

