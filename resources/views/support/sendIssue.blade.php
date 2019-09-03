<div class="row">

              <div class="col-md-12 mb-3">
                <label for="currency">Which Digital Currency did you send?*:</label>
                <select id="currency" name="currency" class="form-control" required>
                    <option value=""disabled selected>Select Type</option>
                    <option value="BTC">BTC</option>
                    <option value="BCH">BCH</option>
                    <option value="ETH">ETH</option>
                    <option value="DOGE">DOGE</option>
                    <option value="LTC">LTC</option>
                    <option value="DASH">DASH</option>
                    <option value="XLM">XLM</option>
                    <option value="XRP">XRP</option>
                    <option value="1LIFE">1LIFE</option>
                </select>
              </div>

              <div class="col-md-12 mb-3">
                <label for="blockExp">Is the transaction showing on a block explorer?*:</label>
                <select id="blockExp" name="blockExp" class="form-control" required>
                    <option value=""disabled selected>Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="I don't know">I don't know</option>
                </select>
              </div>

              <div class="col-md-12 mb-3">
                <label for="depoAddr">Did you send to the correct deposit address?*:</label>
                <select id="depoAddr" name="depoAddr" class="form-control" required>
                    <option value=""disabled selected>Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="I don't know">I don't know</option>
                </select>
              </div>

              <div class="col-md-12 mb-3">
                <label for="date">Transaction date*:</label>
                <input type="date" class="form-control" name="date" id="date" placeholder="" value="" required>
              </div>

      </div>