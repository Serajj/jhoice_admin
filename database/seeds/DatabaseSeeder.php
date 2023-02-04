<?php
/*
 * File name: DatabaseSeeder.php
 * Last modified: 2021.08.10 at 18:03:34
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    //    $files_arr = scandir( dirname(__FILE__) ); //store filenames into $files_array
    //     foreach ($files_arr as $key => $file) {
    //         if ($file !== 'DatabaseSeeder.php' && $file !== "." && $file !== ".." && $file !== "v120" && $file !== "v121" && $file !== "v122" ){
    //             $f =  explode('.', $file)[0]. '::class';
    //             echo 'file '.$f.'-';
    //             $this->call( $f );
    //         }
    //     }
    $this->call([
            UsersTableSeeder::class,
             AddressesTableSeeder::class,
            UploadsTableSeeder::class,
            RefreshPermissionsSeeder::class,
            TaxesTableSeeder::class,
            SlidesTableSeeder::class,
            RolesTableSeeder::class,
            RoleHasPermissionsTableSeeder::class,
            PermissionsTableSeeder::class,
            PaymentStatusesTableSeeder::class,
            PaymentsTableSeeder::class,
            PaymentMethodsTableSeeder::class,
            PasswordResetsTableSeeder::class,
            NotificationsTableSeeder::class,
            ModelHasRolesTableSeeder::class,
            ModelHasPermissionsTableSeeder::class,
            MediaTableSeeder::class,
            FavoritesTableSeeder::class,
            FavoriteOptionsTableSeeder::class,
            EProviderTypesTableSeeder::class,
            EProvidersTableSeeder::class,
            EProvidersPayoutsTableSeeder::class,
            EProviderAddressesTableSeeder::class,
            EarningsTableSeeder::class,
            CustomPagesTableSeeder::class,
            CurrenciesTableSeeder::class,
            CategoriesTableSeeder::class,
            BookingStatusesTableSeeder::class,
            BookingsTableSeeder::class,
            AwardsTableSeeder::class,
            AvailabilityHoursTableSeeder::class,
            AppSettingsTableSeeder::class,

            CustomFieldsTableSeeder::class,
            CustomFieldValuesTableSeeder::class,
              EServicesTableSeeder::class,
            EServiceReviewsTableSeeder::class,
            EServiceCategoriesTableSeeder::class,
             ExperiencesTableSeeder::class,
             FaqCategoriesTableSeeder::class,
             FaqsTableSeeder::class,
             GalleriesTableSeeder::class,
             OptionGroupsTableSeeder::class,
             OptionsTableSeeder::class,
             WalletsTableSeeder::class,
             WalletTransactionsTableSeeder::class,
    ]);
    }
}
