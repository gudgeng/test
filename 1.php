<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System KPI LKTN</title>
   
    <style>
        body {
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
        }
        .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #2f813d;
    color: white;
    padding: 15px 25px;
    font-size: 20px;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

        .menu-icon {
            cursor: pointer;
        }
        .logout-icon {
    font-size: 24px;
    cursor: pointer;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px;
    transition: background-color 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto; /* Ensures it stays within the header */
}

        .sidebar {
            width: 200px;
            background: white;
            padding: 20px;
            position: fixed;
            left: -220px;
            top: 60px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease-in-out;
        }
        .sidebar a {
            display: block;
            text-decoration: none;
            color: #333;
            padding: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        .sidebar a:hover {
            background: #6200ea;
            color: white;
            border-radius: 5px;
            transform: scale(1.05);
        }
        .main-content {
            flex: 1;
            padding: 80px 20px 20px 20px;
            transition: margin-left 0.3s ease-in-out;
            width: 100%;
        }
        .slider {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
            max-width: 80%;
            border-radius: 10px;
            overflow: hidden;
        }
        .slider img {
            display: flex;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .categories {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px;
        }
        .category {
            text-align: center;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .category:hover {
            transform: scale(1.05);
        }
        .category img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }
        .category p {
            font-weight: bold;
            margin-top: 10px;
        }
        .sidebar.open {
            left: 0;
        }
        .main-content.shift {
            margin-left: 220px;          
                /* Existing styles... */
        }
        .logout-icon {
    font-size: 24px;
    cursor: pointer;
    
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px;
    transition: background-color 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto; /* Ensures it stays within the header */ 
            }

            
                .logout-icon:hover {
                    background-color: #c0392b; /* Darker red on hover */
                }
            </style>
            <script>
                function logout() {
                    // Add your logout logic here
                    alert("You have been logged out!");
                    // Redirect to login page (example)
                    window.location.href = "index.php";
                }
            </script>
        
    </style>
    <style>
        /* General icon styling */
        .icon {
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 50%;
            transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        /* Menu icon */
        .menu-icon {
            background-color: #2f813d;
            color: white;
        }

        .menu-icon:hover {
            background-color: #27632d;
            transform: scale(1.1);
        }

        /* Logout icon */
        .logout-icon {
            background-color: #ffffff00; /* Red background for logout */
            color: white;
            border: none;
        }

        .logout-icon:hover {
            background-color: #c0392b; /* Darker red on hover */
            transform: scale(1.1);
        }

        /* Sidebar links hover effect */
        .sidebar a {
            display: block;
            text-decoration: none;
            color: #333;
            padding: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        .sidebar a:hover {
            background: #6200ea;
            color: white;
            border-radius: 5px;
            transform: scale(1.05);
        }

        /* Category icons */
        .category img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            transition: transform 0.3s ease-in-out;
        }

        .category img:hover {
            transform: scale(1.1);
        }

        /* Header icons spacing */
        .header .icon {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="menu-icon" onclick="toggleSidebar()">&#9776; SYSTEM KPI LKTN</div>
        <div class="logout-icon icon" onclick="logout()">&#10148;</div>
    </div>    
    <div class="sidebar" id="sidebar">
        <a href="kenaf.php">KENAF</a>
        <a href="tembakau.html">TEMBAKAU</a>
        <a href="pentadbiran.html">PENTADBIRAN</a>
        <a href="edit_date.php">Set Edit Date</a> <!-- New link -->
    </div>
    <div class="main-content">
        <div class="slider">
            <img src="image/bangunan.jpg" alt="LKTN Building">
        </div>
        <div class="categories">
            <div class="category">
                <div class="buton" onclick="window.location.href='kenaf.php';">    
                <img src="image/kenaf.jpg" alt="Kenaf">
                <p>KENAF</p>
                </div>
            </div>
            <div class="category">
                <div class="buton" onclick="window.location.href='tembakau.php';"> 
                <img src="image/tembakau.jpg" alt="Tembakau">
                <p>TEMBAKAU</p>
                
    <?php
                echo                 $startDate;echo $endDate;  echo$currentDate = date('Y-m-d'); ?>
                </div>
            </div>
            <div class="category">
                <div class="buton" onclick="window.location.href='pentadbiran.php';"> 
                <img src="image/pentadbiran.png" alt="Pentadbiran">
                <p>PENTADBIRAN</p>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("main-content");
            if (sidebar.classList.contains("open")) {
                sidebar.classList.remove("open");
                mainContent.classList.remove("shift");
            } else {
                sidebar.classList.add("open");
                mainContent.classList.add("shift");
            }
        }
    </script>
</body>
</html>
</body>
</html>



