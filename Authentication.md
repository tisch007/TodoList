Authentication
======================

The security configuration is set up in app/config/security.yml

	
```yaml
# app/config/security.yml

security:
    encoders:
        AppBundle\Entity\User: bcrypt

    providers:
        doctrine:
            entity:
                class: AppBundle:User
                property: username

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            anonymous: ~
            pattern: ^/
            form_login:
                login_path: login
                check_path: login_check
                always_use_default_target_path:  true
                default_target_path:  /
            logout: ~

    access_control:
         - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
         - { path: ^/users, roles: ROLE_ADMIN }
         - { path: ^/, roles: ROLE_USER }

    role_hierarchy:
        ROLE_ADMIN: ROLE_USER

```

## Detail on this file

Users are stored in the database.

```yaml
# app/config/security.yml

security:
    providers:
        doctrine:
            entity:
                class: AppBundle:User
                property: username
```


The firewalls security affecte all the url starting by /
```yaml
# app/config/security.yml

security:
	firewalls:
	    main:
	        pattern: ^/
```

Users are authenticate with a login page /login ( controller for login page is in src/Appbundle/Controller/SecurityController.php )

```yaml
# app/config/security.yml

firewalls:
    main:
        anonymous: ~
        pattern: ^/
        form_login:
            login_path: login
            check_path: login_check
            always_use_default_target_path:  true
            default_target_path:  /
        logout: ~
```

The url /login don't require any authentification.

```yaml
# app/config/security.yml

security:
	firewalls:
		access_control:
	    	 { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
```

The url / require to authentificate as User

```yaml
# app/config/security.yml

security:
	firewalls:
    	access_control:
        	 { path: ^/, roles: ROLE_USER }
```

The url /users require to authentificate as Admin

```yaml
# app/config/security.yml

security:
	firewalls:
    	access_control:
        	 { path: ^/users, roles: ROLE_ADMIN }
```