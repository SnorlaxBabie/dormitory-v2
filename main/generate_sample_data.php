<?php
require_once '../vendor/autoload.php';
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
use Faker\Factory;

$faker = Factory::create();
$tenants = [];
error_reporting(E_ALL);
ini_set('display_errors', 1);

for ($i = 0; $i < 70; $i++) {
    $isVacated = rand(0, 1);
    $vacatedDate = $isVacated ? $faker->dateTimeBetween('2024-01-01', '2026-11-01')->format('Y-m-d') : null;
    
    // Create tenant data
    $tenants[] = [
        'usr_cde' => $faker->unique()->userName,
        'usr_fname' => $faker->firstName,
        'usr_mname' => $faker->firstName,
        'usr_lname' => $faker->lastName,
        'usr_sex' => $faker->randomElement(['Male', 'Female']),
        'age' => $faker->numberBetween(18, 60),
        'usr_email' => $faker->unique()->safeEmail,
        'usr_contactnum' => $faker->phoneNumber,
        'usr_name' => $faker->unique()->userName,
        'usr_pwd' => sha1('password123'),
        'usr_brgy' => $faker->streetAddress,
        'usr_municipality' => $faker->city,
        'usr_province' => $faker->state,
        'usr_havemedcondition' => rand(0, 1),
        'trmscheck' => rand(0, 1),
        'eci_fullname' => $faker->name,
        'eci_relationship' => $faker->randomElement(['Parent', 'Sibling', 'Friend']),
        'eci_contactnum' => $faker->phoneNumber,
        'eci_homenum' => $faker->phoneNumber,
        'eci_worknum' => $faker->phoneNumber,
        'eci_address' => $faker->address,
        'usr_status' => rand(0, 1),
        'vacated' => $isVacated,
        'vacated_date' => $vacatedDate,
        'deposit' => $faker->randomFloat(2, 100, 1000),
        'paid' => $faker->randomFloat(2, 0, 1000),
        'balance' => $faker->randomFloat(2, 0, 1000),
        'roomnum' => $faker->word,
        'bedspacenum' => $faker->word,
        'startlease' => $faker->dateTimeBetween('2024-01-03', '2025-11-03')->format('Y-m-d'),
        'endlease' => $faker->dateTimeBetween('2027-01-01', '2028-12-31')->format('Y-m-d'),
        'usr_versionuse' => 'v1.0',
        'usr_lvl' => 'USER',
        'roomid' => $faker->word,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ];
}

// Prepare a SQL statement with the right number of placeholders
$sql = "INSERT INTO userfile (
    usr_cde, usr_fname, usr_mname, usr_lname, usr_sex, age, usr_email, usr_contactnum,
    usr_name, usr_pwd, usr_brgy, usr_municipality, usr_province, usr_havemedcondition,
    trmscheck, eci_fullname, eci_relationship, eci_contactnum, eci_homenum, eci_worknum,
    eci_address, usr_status, vacated, vacated_date, deposit, paid, balance, roomnum,
    bedspacenum, startlease, endlease, usr_versionuse, usr_lvl, roomid,
    created_at, updated_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?,?)";

$stmt = $connect->prepare($sql);

foreach ($tenants as $tenant) {
    // Ensure the exact number of values matches the placeholders defined in the SQL statement.
    $stmt->execute([
        $tenant['usr_cde'], 
        $tenant['usr_fname'], 
        $tenant['usr_mname'], 
        $tenant['usr_lname'], 
        $tenant['usr_sex'], 
        $tenant['age'], 
        $tenant['usr_email'], 
        $tenant['usr_contactnum'], 
        $tenant['usr_name'], 
        $tenant['usr_pwd'], 
        $tenant['usr_brgy'], 
        $tenant['usr_municipality'], 
        $tenant['usr_province'], 
        $tenant['usr_havemedcondition'], 
        $tenant['trmscheck'], 
        $tenant['eci_fullname'], 
        $tenant['eci_relationship'], 
        $tenant['eci_contactnum'], 
        $tenant['eci_homenum'], 
        $tenant['eci_worknum'], 
        $tenant['eci_address'], 
        $tenant['usr_status'], 
        $tenant['vacated'], 
        $tenant['vacated_date'], 
        $tenant['deposit'], 
        $tenant['paid'], 
        $tenant['balance'], 
        $tenant['roomnum'], 
        $tenant['bedspacenum'], 
        $tenant['startlease'], 
        $tenant['endlease'], 
        $tenant['usr_versionuse'], 
        $tenant['usr_lvl'], 
        $tenant['roomid'], 
        $tenant['created_at'], 
        $tenant['updated_at']
    ]);
}
?>