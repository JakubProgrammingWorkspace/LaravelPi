How to Run the App                                                                                                                                                                                    
                                                                                                                                                                                                       
 ### Prerequisites                                                                                                                                                                                     
                                                                                                                                                                                                       
 - Docker and Docker Compose installed                                                                                                                                                                 
                                                                                                                                                                                                       
 ### Steps                                                                                                                                                                                             
                                                                                                                                                                                                       
 ```bash                                                                                                                                                                                               
   # 1. Start the containers (builds + runs)                                                                                                                                                           
   cd /home/jakub/workspace/LaravelPi                                                                                                                                                                  
   docker compose up -d --build                                                                                                                                                                        
                                                                                                                                                                                                       
   # 2. Install PHP dependencies (one-time)                                                                                                                                                            
   docker compose exec laravel_app composer install --no-interaction                                                                                                                                   
                                                                                                                                                                                                       
   # 3. Generate the application key (one-time)                                                                                                                                                        
   docker compose exec laravel_app php artisan key:generate --no-interaction                                                                                                                           
                                                                                                                                                                                                       
   # 4. Run database migrations and seed admin data (one-time)                                                                                                                                         
   docker compose exec laravel_app php artisan migrate:fresh --seed                                                                                                                                    
                                                                                                                                                                                                       
   # 5. Open in browser                                                                                                                                                                                
   #    http://localhost:8080/login                                                                                                                                                                    
   #    Login: admin@hrportal.local  |  Password: password                                                                                                                                             
 ```                                                                                                                                                                                                   
                                                                                                                                                                                                       
 ### Key endpoints                                                                                                                                                                                     
                                                                                                                                                                                                       
 ┌───────────────────────────────────────┬─────────────────────────────────────┐                                                                                                                       
 │ URL                                   │ Description                         │                                                                                                                       
 ├───────────────────────────────────────┼─────────────────────────────────────┤                                                                                                                       
 │ http://localhost:8080/login           │ Login page                          │                                                                                                                       
 ├───────────────────────────────────────┼─────────────────────────────────────┤                                                                                                                       
 │ http://localhost:8080/panel/employees │ Employee management (any auth user) │                                                                                                                       
 ├───────────────────────────────────────┼─────────────────────────────────────┤                                                                                                                       
 │ http://localhost:8080/panel/users     │ User management (admin only)        │                                                                                                                       
 ├───────────────────────────────────────┼─────────────────────────────────────┤                                                                                                                       
 │ http://localhost:8080/panel/roles     │ Role management (admin only)        │                                                                                                                       
 └───────────────────────────────────────┴─────────────────────────────────────┘                                                                                                                       
                                                                                                                                                                                                       
 ### Useful commands                                                                                                                                                                                   
                                                                                                                                                                                                       
 ```bash                                                                                                                                                                                               
   docker compose logs -f          # View logs                                                                                                                                                         
   docker compose down -v          # Stop + remove volumes (fresh start)                                                                                                                               
   docker compose restart          # Restart containers                      
