<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .logout-button {
    text-decoration: none;
    background-color: #dc3545;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .view-button {
    text-decoration: none;
    background-color: #0c6cc6;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .view-button {
    padding: 13px 15px;
  }

  .top-logout {
    position: absolute;
    top: 20px;
    right: 20px;
  }

  .table-layout {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-top: 5px;
    padding: 20px;
    flex-grow: 1;
  }

  .table-link {
    flex: 0 calc(15% - 10px);
    text-decoration: none;
  }

  .table {
    width: 100%;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;

    color: white;
    font-weight: bold;
    border-radius: 20px;
    background-color: #28a745;
  }

  @media (max-width: 1024px) {
    .table-link {
      flex: 0 1 calc(25% - 10px);
    }
  }

  @media (max-width: 768px) {
    .logo {
      align-content: first baseline;
      width: 18%;
      height: 18%;
    }

    .table-link {
      flex: 0 1 calc(33.33% - 10px);
    }

    .payment_details {
      width: 100%;
      height: 100%;
      display: block;
      justify-content: center;
      align-items: center;
    }
  }

  @media (max-width: 480px) {
    .table-link {
      flex: 0 1 calc(33.33% - 10px);
    }

  }

  .main-menu-grid,
  .other-menu-grid {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
  }

  .menu-card img {
    height: 160px !important;
    width: 100% !important;
    border-radius: 3px;
    object-fit: cover;
  }

  .menu-card h5 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .menu-card .p-4 {
    padding: 8px !important;
  }

  .menu-card .p-2 {
    padding: 8px !important;
  }

  .menu-image-container {
    width: 100%;
    height: 160px;
    overflow: hidden;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  .menu-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .menu-cards-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    width: 100px;
    justify-content: center;
    max-width: 100%;
    overflow-x: hidden;
  }

  .bottom-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 20px;
  }

  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  .modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 700px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-sizing: border-box;
  }

  .close-modal {
    position: absolute;
    top: 10px;
    right: 20px;
    cursor: pointer;
    font-size: 20px;
  }


  input[type="text"],
  input[type="number"],
  input[type="time"],
  textarea {
    padding: 5px;
    margin-top: 4px;
    margin-bottom: 10px;
    width: 100%;
  }



  label {
    font-size: 14px;
  }

  .modal-section {
    margin-bottom: 15px;
  }

  .modal-flex {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

  .modal-column {
    flex: 1 1 200px;
  }

  .modal-actions {
    display: flex;
    justify-content: center;
    margin-top: 20px;
  }

  .pay-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    color: white;
    background-color: #6c757d;
    cursor: pointer;
  }

  .order-input {
    margin-top: 5px;
  }

  .table {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .table-number {
    font-weight: bold;
    margin-bottom: 5px;
  }
</style>